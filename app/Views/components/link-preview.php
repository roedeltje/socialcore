<?php
/**
 * Core Link Preview Component
 * /app/Views/components/link-preview.php
 * 
 * Independent link preview component for Core Timeline
 * Self-contained with inline styling for theme independence
 */

// Validate required data
if (empty($post['link_preview_id']) || empty($post['preview_url'])) {
    return;
}

// Safe data extraction with fallbacks
$previewUrl = htmlspecialchars($post['preview_url']);
$previewDomain = htmlspecialchars($post['preview_domain'] ?? parse_url($post['preview_url'], PHP_URL_HOST) ?? 'Website');
$previewTitle = !empty($post['preview_title']) ? htmlspecialchars($post['preview_title']) : null;
$previewDescription = !empty($post['preview_description']) ? htmlspecialchars(substr($post['preview_description'], 0, 120)) : null;
$previewImage = !empty($post['preview_image']) ? htmlspecialchars($post['preview_image']) : null;
$hasLongDescription = !empty($post['preview_description']) && strlen($post['preview_description']) > 120;
?>

<div class="core-link-preview">
    <a href="<?= $previewUrl ?>" 
       target="_blank" 
       rel="noopener noreferrer"
       class="core-link-preview-card">
        
        <div class="core-link-preview-layout">
            <!-- Content Section (Left) -->
            <div class="core-link-preview-content">
                <!-- Domain -->
                <div class="core-link-preview-domain">
                    📌 <?= $previewDomain ?>
                </div>
                
                <!-- Title -->
                <?php if ($previewTitle): ?>
                    <div class="core-link-preview-title">
                        <?= $previewTitle ?>
                    </div>
                <?php endif; ?>
                
                <!-- Description -->
                <?php if ($previewDescription): ?>
                    <div class="core-link-preview-description">
                        <?= $previewDescription ?><?= $hasLongDescription ? '...' : '' ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Image Section (Right) -->
            <?php if ($previewImage): ?>
                <div class="core-link-preview-image">
                    <img src="<?= $previewImage ?>" 
                         alt="<?= $previewTitle ?? 'Link preview' ?>"
                         loading="lazy"
                         onerror="this.parentElement.style.display='none'">
                </div>
            <?php endif; ?>
        </div>
    </a>
</div>

<style>
/* Core Link Preview Styles - Self-contained */
.core-link-preview {
    border: 2px solid #0f3ea3;
    border-radius: 10px;
    overflow: hidden;
    background: white;
    margin-top: 15px;
    transition: all 0.2s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.core-link-preview:hover {
    background: linear-gradient(135deg, #f0f4ff 0%, #e6efff 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(15, 62, 163, 0.2);
}

.core-link-preview-card {
    display: block;
    text-decoration: none;
    color: inherit;
    transition: all 0.2s ease;
}

.core-link-preview-card:hover {
    text-decoration: none;
    color: inherit;
}

.core-link-preview-layout {
    display: flex;
    align-items: flex-start;
    gap: 0;
    min-height: 80px;
}

.core-link-preview-content {
    flex: 1;
    padding: 15px;
    min-width: 0;
}

.core-link-preview-domain {
    font-size: 12px;
    color: #0f3ea3;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.core-link-preview-title {
    font-size: 15px;
    font-weight: 700;
    color: #0f3ea3;
    margin-bottom: 6px;
    line-height: 1.4;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    word-break: break-word;
}

.core-link-preview-description {
    font-size: 13px;
    color: #4b5563;
    line-height: 1.5;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    word-break: break-word;
}

.core-link-preview-image {
    width: 120px;
    height: 120px;
    flex-shrink: 0;
    overflow: hidden;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
}

.core-link-preview-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.2s ease;
}

.core-link-preview:hover .core-link-preview-image img {
    transform: scale(1.05);
}

/* Responsive Design */
@media (max-width: 640px) {
    .core-link-preview-layout {
        flex-direction: column;
    }
    
    .core-link-preview-image {
        width: 100%;
        height: 150px;
        order: -1;
    }
    
    .core-link-preview-content {
        padding: 12px 15px;
    }
    
    .core-link-preview-title {
        font-size: 14px;
    }
    
    .core-link-preview-description {
        font-size: 12px;
    }
}

/* No image variant */
.core-link-preview:not(:has(.core-link-preview-image)) .core-link-preview-content {
    padding: 15px 20px;
}
</style>