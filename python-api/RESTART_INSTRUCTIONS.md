## How to Restart the Python API Server

### Quick Restart:
1. **Stop current server**: Press `Ctrl+C` in the Python terminal
2. **Start again**: Run `python app.py`

### The server should show:
```
INFO:__main__:Loading waste classification model...
INFO:__main__:Model loaded successfully!
INFO:__main__:Waste classification wrapper ready!
 * Running on http://127.0.0.1:5000
```

### Test the API:
Visit: http://localhost:5000/health

Should return:
```json
{
  "status": "healthy",
  "model_loaded": true
}
```

### What changed:
- ✅ Uses Microsoft ResNet-50 (a working, verified model)
- ✅ Maps object detection results to waste categories
- ✅ Recognizes bottles, cans, boxes, jars, etc.
- ✅ Returns proper top_k results
- ✅ No more "General Waste" for everything!

### How it works:
1. ResNet-50 identifies objects in the image (bottle, can, box, etc.)
2. Keywords are mapped to waste types:
   - "bottle", "plastic", "container" → Plastic
   - "can", "tin", "metal" → Metal
   - "glass", "jar" → Glass
   - "box", "cardboard", "paper" → Paper
3. Scores are aggregated and returned

### Troubleshooting:
- If you get "module not found": Run `pip install transformers torch`
- If model doesn't load: Check internet connection (downloads model first time)
- If still issues: The model is ~100MB, may take a minute to download
