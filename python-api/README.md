# Trash-Net AI Classification API

This Python Flask API serves the Hugging Face Trash-Net model for waste classification.

## Setup

1. **Create a virtual environment:**
   ```bash
   python -m venv venv
   ```

2. **Activate the virtual environment:**
   - Windows:
     ```bash
     venv\Scripts\activate
     ```
   - Linux/Mac:
     ```bash
     source venv/bin/activate
     ```

3. **Install dependencies:**
   ```bash
   pip install -r requirements.txt
   ```

4. **Run the API:**
   ```bash
   python app.py
   ```

The API will start on `http://localhost:5000`

## Endpoints

### Health Check
- **URL:** `/health`
- **Method:** GET
- **Response:** 
  ```json
  {
    "status": "healthy",
    "model_loaded": true
  }
  ```

### Classify Waste
- **URL:** `/classify`
- **Method:** POST
- **Content-Type:** `multipart/form-data` or `application/json`
- **Body (file upload):**
  ```
  image: [image file]
  ```
- **Body (base64):**
  ```json
  {
    "image": "base64_encoded_image_string"
  }
  ```
- **Response:**
  ```json
  {
    "success": true,
    "waste_type": "Plastic",
    "confidence": 0.95,
    "raw_label": "plastic",
    "all_predictions": [...]
  }
  ```

## Supported Waste Categories

- Plastic
- Paper
- Metal
- Glass
- General Waste

## Model Information

- **Model:** prithivMLmods/Trash-Net
- **Type:** Image Classification
- **Source:** Hugging Face
