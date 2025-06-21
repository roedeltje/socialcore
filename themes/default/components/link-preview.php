<?php
// Bestand: /themes/default/components/link-preview.php
// Link preview component voor posts

if (!isset($post) || empty($post['preview_url'])) return;
?>

<div class="link-preview mt-3">
    <a href="<?= htmlspecialchars($post['preview_url']) ?>" 
       target="_blank" 
       rel="noopener noreferrer"
       class="link-preview-card">
        
        <div class="link-preview-layout">
            <!-- Preview content (tekst gedeelte) -->
            <div class="link-preview-content">
                <!-- Domain -->
                <div class="link-preview-domain">
                    📌 <?= htmlspecialchars($post['preview_domain'] ?? parse_url($post['preview_url'], PHP_URL_HOST)) ?>
                </div>
                
                <!-- Titel -->
                <?php if (!empty($post['preview_title'])): ?>
                    <div class="link-preview-title">
                        <?= htmlspecialchars($post['preview_title']) ?>
                    </div>
                <?php endif; ?>
                
                <!-- Beschrijving -->
                <?php if (!empty($post['preview_description'])): ?>
                    <div class="link-preview-description">
                        <?= htmlspecialchars(substr($post['preview_description'], 0, 120)) ?>
                        <?= strlen($post['preview_description']) > 120 ? '...' : '' ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Preview afbeelding (rechts) -->
            <?php if (!empty($post['preview_image'])): ?>
                <div class="link-preview-image">
                    <img src="<?= htmlspecialchars($post['preview_image']) ?>" 
                         alt="<?= htmlspecialchars($post['preview_title'] ?? 'Link preview') ?>"
                         loading="lazy">
                </div>
            <?php endif; ?>
        </div>
    </a>
</div>

<style>
.link-preview {
    border: 2px solid #0f3ea3;
    border-radius: 10px;
    overflow: hidden;
    background: white;
    transition: all 0.2s ease;
}

.link-preview-card {
    display: block;
    text-decoration: none;
    color: inherit;
    transition: all 0.2s ease;
}

.link-preview-card:hover {
    background: linear-gradient(135deg, #f0f4ff 0%, #e6efff 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(15, 62, 163, 0.2);
}

.link-preview-layout {
    display: flex;
    align-items: flex-start;
    gap: 0;
}

.link-preview-content {
    flex: 1;
    padding: 15px;
    min-width: 0; /* Voorkomt flexbox overflow */
}

.link-preview-image {
    width: 120px;
    height: 120px;
    flex-shrink: 0;
    overflow: hidden;
    background: #f3f4f6;
}

.link-preview-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.link-preview-domain {
    font-size: 12px;
    color: #0f3ea3;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 6px;
}

.link-preview-title {
    font-size: 15px;
    font-weight: 700;
    color: #0f3ea3;
    margin-bottom: 6px;
    line-height: 1.4;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.link-preview-description {
    font-size: 13px;
    color: #4b5563;
    line-height: 1.5;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

/* Responsive design */
@media (max-width: 640px) {
    .link-preview-layout {
        flex-direction: column;
    }
    
    .link-preview-image {
        width: 100%;
        height: 150px;
        order: -1; /* Afbeelding bovenaan op mobiel */
    }
    
    .link-preview-content {
        padding: 12px 15px;
    }
    
    .link-preview-title {
        font-size: 14px;
    }
    
    .link-preview-description {
        font-size: 12px;
    }
}

/* Variant zonder afbeelding */
.link-preview:not(:has(.link-preview-image)) .link-preview-content {
    padding: 15px 20px;
}
</style>