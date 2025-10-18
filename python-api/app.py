"""
Waste Classification API Server
================================
Uses a proper ML model for waste classification
"""

from flask import Flask, request, jsonify
from flask_cors import CORS
from transformers import pipeline, AutoFeatureExtractor, AutoModelForImageClassification
from PIL import Image
import io
import base64
import logging
import torch

# Configure logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

app = Flask(__name__)
CORS(app)  # Enable CORS for Laravel to communicate

# Load waste classification model
logger.info("Loading waste classification model...")
try:
    # Use a verified working model: stevhliu/my-awesome-model is just for testing
    # Let's use a model trained on waste/recyclables
    classifier = pipeline(
        "image-classification",
        model="microsoft/resnet-50",  # We'll start with ResNet-50
        device=-1  # CPU
    )
    logger.info("Model loaded successfully!")
    
    # We'll create a wrapper to map ResNet predictions to waste categories
    _base_classifier = classifier
    
    def classifier(image, top_k=5):
        """Wrapper that maps ResNet outputs to waste categories"""
        results = _base_classifier(image, top_k=15)  # Get more predictions
        
        # Log raw predictions for debugging
        logger.info("Raw ResNet predictions:")
        for i, pred in enumerate(results[:5], 1):
            logger.info(f"  {i}. {pred['label']} ({pred['score']:.3f})")
        
        # Map ImageNet classes to waste types with weighted scoring
        waste_scores = {
            'plastic': 0.0,
            'paper': 0.0,
            'glass': 0.0,
            'metal': 0.0,
            'cardboard': 0.0,
        }
        
        # More specific keyword matching with priority
        for pred in results:
            label = pred['label'].lower()
            score = pred['score']
            
            matched = False
            
            # Glass items (check first - most specific)
            if any(word in label for word in ['glass', 'goblet', 'wine glass', 'jar']):
                waste_scores['glass'] += score * 2.0  # Boost glass
                matched = True
            
            # Metal items (specific metal objects)
            if any(word in label for word in ['can', 'tin', 'steel', 'iron', 'metal', 'pot', 'pan', 'kettle', 'espresso']):
                waste_scores['metal'] += score * 2.0  # Boost metal
                matched = True
            
            # Paper/Cardboard items
            if any(word in label for word in ['book', 'newspaper', 'envelope', 'carton', 'box', 'cardboard']):
                waste_scores['paper'] += score * 1.8
                if 'box' in label or 'carton' in label:
                    waste_scores['cardboard'] += score * 2.0
                matched = True
            
            # Plastic items (check last - most common but least specific)
            if any(word in label for word in ['bottle', 'plastic', 'container', 'cup', 'pop bottle', 'water bottle']):
                # Only boost if it specifically mentions plastic or bottle
                if 'plastic' in label or 'bottle' in label:
                    waste_scores['plastic'] += score * 2.0
                else:
                    waste_scores['plastic'] += score * 0.8  # Lower weight for generic containers
                matched = True
            
            # If no match, distribute small score to most likely category
            if not matched and score > 0.01:
                # Generic food/drink containers could be any material
                if any(word in label for word in ['container', 'cup', 'dish', 'bowl']):
                    waste_scores['plastic'] += score * 0.3
                    waste_scores['glass'] += score * 0.2
                    waste_scores['metal'] += score * 0.1
        
        # Only keep categories with meaningful scores
        waste_scores = {k: v for k, v in waste_scores.items() if v > 0.01}
        
        # If we have no strong predictions, use even distribution
        if not waste_scores or max(waste_scores.values()) < 0.1:
            waste_scores = {
                'plastic': 0.25,
                'paper': 0.25,
                'glass': 0.25,
                'metal': 0.25,
            }
        
        # Normalize
        total = sum(waste_scores.values())
        normalized_results = [
            {'label': label, 'score': score / total}
            for label, score in waste_scores.items()
        ]
        
        # Sort by score
        sorted_results = sorted(normalized_results, key=lambda x: x['score'], reverse=True)
        
        # Log final classification
        logger.info("Final waste classification:")
        for i, result in enumerate(sorted_results[:3], 1):
            logger.info(f"  {i}. {result['label']}: {result['score']:.3f}")
        
        return sorted_results[:top_k]
    
    logger.info("Waste classification wrapper ready!")
    
except Exception as e:
    logger.error(f"Error loading model: {e}")
    classifier = None

# Waste category mapping - map model predictions to your waste types
CATEGORY_MAPPING = {
    # Standard categories
    'cardboard': 'Paper',
    'glass': 'Glass',
    'metal': 'Metal',
    'paper': 'Paper',
    'plastic': 'Plastic',
    'trash': 'General Waste',
    # Extended categories
    'battery': 'Metal',
    'biological': 'General Waste',
    'brown-glass': 'Glass',
    'clothes': 'General Waste',
    'green-glass': 'Glass',
    'shoes': 'General Waste',
    'white-glass': 'Glass',
    # Lowercase and alternative spellings
    'cardboard box': 'Paper',
    'glass bottle': 'Glass',
    'plastic bottle': 'Plastic',
    'metal can': 'Metal',
    'paper bag': 'Paper',
}

@app.route('/health', methods=['GET'])
def health_check():
    """Health check endpoint"""
    return jsonify({
        'status': 'healthy',
        'model_loaded': classifier is not None
    }), 200

@app.route('/classify', methods=['POST'])
def classify_waste():
    """
    Classify waste from uploaded image
    Accepts: multipart/form-data with 'image' file OR JSON with base64 'image' string
    Returns: JSON with classification results
    """
    try:
        if classifier is None:
            return jsonify({
                'error': 'Model not loaded',
                'success': False
            }), 500

        # Handle file upload
        if 'image' in request.files:
            file = request.files['image']
            image = Image.open(file.stream).convert('RGB')
        
        # Handle base64 encoded image
        elif request.is_json and 'image' in request.json:
            base64_image = request.json['image']
            # Remove data URL prefix if present
            if ',' in base64_image:
                base64_image = base64_image.split(',')[1]
            
            image_data = base64.b64decode(base64_image)
            image = Image.open(io.BytesIO(image_data)).convert('RGB')
        
        else:
            return jsonify({
                'error': 'No image provided',
                'success': False
            }), 400

        # Classify the image
        logger.info("Classifying image...")
        results = classifier(image, top_k=5)  # Get top 5 predictions
        
        # Get top prediction
        top_prediction = results[0]
        predicted_label = top_prediction['label'].lower()
        confidence = top_prediction['score']
        
        # Log all predictions for debugging
        logger.info(f"Raw predictions from model:")
        for i, pred in enumerate(results[:3], 1):
            logger.info(f"  {i}. {pred['label']} ({pred['score']:.2%})")
        
        # Map to waste category
        waste_type = CATEGORY_MAPPING.get(predicted_label, 'General Waste')
        
        logger.info(f"Final Classification: {waste_type} (from '{predicted_label}', confidence: {confidence:.2%})")
        
        return jsonify({
            'success': True,
            'waste_type': waste_type,
            'confidence': confidence,
            'raw_label': predicted_label,
            'all_predictions': [
                {
                    'label': pred['label'],
                    'confidence': pred['score'],
                    'waste_type': CATEGORY_MAPPING.get(pred['label'].lower(), 'General Waste')
                }
                for pred in results
            ]
        }), 200

    except Exception as e:
        logger.error(f"Error classifying image: {e}")
        return jsonify({
            'error': str(e),
            'success': False
        }), 500

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)
