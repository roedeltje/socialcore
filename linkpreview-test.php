<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link Preview Test - SocialCore</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .test-container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        input[type="url"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            box-sizing: border-box;
        }
        button {
            background: #1da1f2;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            font-weight: 600;
        }
        button:hover {
            background: #1991db;
        }
        button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        .loading {
            display: none;
            margin: 20px 0;
            color: #666;
        }
        .preview-result {
            margin-top: 30px;
            padding: 20px;
            border: 2px solid #e1e8ed;
            border-radius: 12px;
            background: #f8f9fa;
            display: none;
        }
        .preview-card {
            border: 1px solid #e1e8ed;
            border-radius: 8px;
            overflow: hidden;
            background: white;
            display: flex;
            max-width: 500px;
        }
        .preview-image {
            width: 120px;
            height: 80px;
            object-fit: cover;
            flex-shrink: 0;
        }
        .preview-content {
            padding: 12px;
            flex: 1;
        }
        .preview-title {
            font-weight: 600;
            color: #14171a;
            margin-bottom: 4px;
            font-size: 14px;
            line-height: 1.3;
        }
        .preview-description {
            color: #657786;
            font-size: 13px;
            line-height: 1.3;
            margin-bottom: 4px;
        }
        .preview-domain {
            color: #657786;
            font-size: 12px;
            text-transform: lowercase;
        }
        .error {
            color: #e0245e;
            background: #fdf2f2;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #fecaca;
        }
        .success {
            color: #0f7f0f;
            background: #f0f9f0;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #bbf7d0;
        }
        .debug-info {
            margin-top: 20px;
            padding: 15px;
            background: #f1f3f4;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            max-height: 300px;
            overflow-y: auto;
        }
        .test-urls {
            margin-bottom: 20px;
        }
        .test-url-btn {
            background: #17bf63;
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            font-size: 12px;
            margin: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="test-container">
        <h1>🔗 Link Preview Test</h1>
        <p>Test de LinkPreviewController om te zien of metadata correct wordt opgehaald.</p>
        
        <div class="test-urls">
            <strong>Snel testen met:</strong><br>
            <button class="test-url-btn" onclick="setTestUrl('https://github.com')">GitHub</button>
            <button class="test-url-btn" onclick="setTestUrl('https://www.youtube.com/watch?v=dQw4w9WgXcQ')">YouTube</button>
            <button class="test-url-btn" onclick="setTestUrl('https://www.nu.nl')">Nu.nl</button>
            <button class="test-url-btn" onclick="setTestUrl('https://www.wikipedia.org')">Wikipedia</button>
        </div>

        <form id="testForm">
            <div class="form-group">
                <label for="url">URL om te testen:</label>
                <input type="url" id="url" name="url" 
                       placeholder="https://example.com" 
                       required>
            </div>
            <button type="submit" id="testBtn">🔍 Genereer Preview</button>
        </form>

        <div class="loading" id="loading">
            ⏳ Bezig met ophalen van website metadata...
        </div>

        <div class="preview-result" id="result">
            <!-- Preview card wordt hier getoond -->
        </div>

        <div class="debug-info" id="debug" style="display: none;">
            <!-- Debug informatie wordt hier getoond -->
        </div>
    </div>

    <script>
        document.getElementById('testForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const url = document.getElementById('url').value;
            const testBtn = document.getElementById('testBtn');
            const loading = document.getElementById('loading');
            const result = document.getElementById('result');
            const debug = document.getElementById('debug');
            
            // Reset UI
            testBtn.disabled = true;
            testBtn.textContent = '⏳ Bezig...';
            loading.style.display = 'block';
            result.style.display = 'none';
            debug.style.display = 'none';
            
            try {
                // Maak FormData voor POST request
                const formData = new FormData();
                formData.append('url', url);
                
                // Call LinkPreview API
                const response = await fetch('?route=linkpreview/generate', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                // Show debug info
                debug.innerHTML = '<strong>API Response:</strong><br>' + 
                                JSON.stringify(data, null, 2);
                debug.style.display = 'block';
                
                if (data.success && data.preview) {
                    showPreviewCard(data.preview, data.cached);
                } else {
                    showError(data.error || 'Onbekende fout opgetreden');
                }
                
            } catch (error) {
                console.error('Error:', error);
                showError('Netwerk fout: ' + error.message);
                debug.innerHTML = '<strong>JavaScript Error:</strong><br>' + error.message;
                debug.style.display = 'block';
            }
            
            // Reset button
            testBtn.disabled = false;
            testBtn.textContent = '🔍 Genereer Preview';
            loading.style.display = 'none';
        });
        
        function showPreviewCard(preview, cached) {
            const result = document.getElementById('result');
            
            const cacheInfo = cached ? 
                '<div class="success">✅ Preview uit cache geladen</div>' : 
                '<div class="success">✅ Nieuwe preview gegenereerd</div>';
            
            const imageHtml = preview.image_url ? 
                `<img src="${preview.image_url}" alt="Preview" class="preview-image" onerror="this.style.display='none'">` : '';
            
            result.innerHTML = `
                ${cacheInfo}
                <div class="preview-card">
                    ${imageHtml}
                    <div class="preview-content">
                        <div class="preview-title">${escapeHtml(preview.title || 'Geen titel')}</div>
                        <div class="preview-description">${escapeHtml(preview.description || 'Geen beschrijving')}</div>
                        <div class="preview-domain">${escapeHtml(preview.domain || 'Onbekend domein')}</div>
                    </div>
                </div>
            `;
            
            result.style.display = 'block';
        }
        
        function showError(message) {
            const result = document.getElementById('result');
            result.innerHTML = `<div class="error">❌ ${escapeHtml(message)}</div>`;
            result.style.display = 'block';
        }
        
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        function setTestUrl(url) {
            document.getElementById('url').value = url;
        }
    </script>
</body>
</html>