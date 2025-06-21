<?php
// Bestand: /themes/twitter/components/link-preview.php
// Twitter-stijl link preview component voor posts

if (!isset($post) || empty($post['preview_url'])) return;
?>

<div class="link-preview mt-3">
    <a href="<?= htmlspecialchars($post['preview_url']) ?>" 
       target="_blank" 
       rel="noopener noreferrer"
       class="link-preview-card">
        
        <div class="link-preview-layout">
            <!-- Preview afbeelding (bovenaan, als Twitter) -->
            <?php if (!empty($post['preview_image'])): ?>
                <div class="link-preview-image">
                    <img src="<?= htmlspecialchars($post['preview_image']) ?>" 
                         alt="<?= htmlspecialchars($post['preview_title'] ?? 'Link preview') ?>"
                         loading="lazy">
                </div>
            <?php endif; ?>
            
            <!-- Preview content (onder afbeelding) -->
            <div class="link-preview-content">
                <!-- Domain - subtiel grijs zoals Twitter -->
                <div class="link-preview-domain">
                    <?= htmlspecialchars($post['preview_domain'] ?? parse_url($post['preview_url'], PHP_URL_HOST)) ?>
                </div>
                
                <!-- Titel - Bold zwart zoals Twitter -->
                <?php if (!empty($post['preview_title'])): ?>
                    <div class="link-preview-title">
                        <?= htmlspecialchars($post['preview_title']) ?>
                    </div>
                <?php endif; ?>
                
                <!-- Beschrijving - subtiel grijs -->
                <?php if (!empty($post['preview_description'])): ?>
                    <div class="link-preview-description">
                        <?= htmlspecialchars(substr($post['preview_description'], 0, 120)) ?>
                        <?= strlen($post['preview_description']) > 120 ? '...' : '' ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </a>
</div>

<style>
/* ===== TWITTER-STIJL LINK PREVIEW ===== */
.link-preview {
    border: 1px solid #e1e8ed;
    border-radius: 16px;
    overflow: hidden;
    background: #ffffff;
    transition: all 0.2s ease-in-out;
    margin-top: 12px;
}

.link-preview-card {
    display: block;
    text-decoration: none;
    color: inherit;
    transition: all 0.2s ease-in-out;
}

.link-preview-card:hover {
    background: #f7f9fa;
    border-color: #d1d9e0;
    text-decoration: none;
    color: inherit;
}

.link-preview-layout {
    display: flex;
    flex-direction: column;
}

/* Afbeelding bovenaan (Twitter-stijl) */
.link-preview-image {
    width: 100%;
    height: 200px;
    overflow: hidden;
    background: #f7f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
}

.link-preview-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.2s ease-in-out;
}

.link-preview-card:hover .link-preview-image img {
    transform: scale(1.02);
}

/* Content gebied */
.link-preview-content {
    padding: 12px 16px 16px 16px;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

/* Domain - Twitter grijs */
.link-preview-domain {
    font-size: 13px;
    color: #657786;
    font-weight: 400;
    line-height: 1.3;
    text-transform: lowercase;
}

/* Titel - Twitter zwart en bold */
.link-preview-title {
    font-size: 15px;
    font-weight: 700;
    color: #14171a;
    line-height: 1.3;
    margin: 2px 0;
    
    /* Text clamping voor lange titels */
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

/* Beschrijving - subtiel grijs */
.link-preview-description {
    font-size: 13px;
    color: #657786;
    line-height: 1.4;
    font-weight: 400;
    margin-top: 2px;
    
    /* Text clamping */
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

/* Hover effecten voor tekst */
.link-preview-card:hover .link-preview-title {
    color: #1da1f2;
}

/* Responsieve aanpassingen */
@media (max-width: 480px) {
    .link-preview-image {
        height: 160px;
    }
    
    .link-preview-content {
        padding: 10px 12px 12px 12px;
    }
    
    .link-preview-title {
        font-size: 14px;
    }
    
    .link-preview-description {
        font-size: 12px;
    }
    
    .link-preview-domain {
        font-size: 12px;
    }
}

/* Dark mode ondersteuning (optioneel voor later) */
@media (prefers-color-scheme: dark) {
    .link-preview {
        border-color: #2f3336;
        background: #16181c;
    }
    
    .link-preview-card:hover {
        background: #1c1f23;
        border-color: #3d4147;
    }
    
    .link-preview-title {
        color: #ffffff;
    }
    
    .link-preview-card:hover .link-preview-title {
        color: #1d9bf0;
    }
    
    .link-preview-description,
    .link-preview-domain {
        color: #8b98a5;
    }
    
    .link-preview-image {
        background: #202327;
    }
}

/* Variant zonder afbeelding - compacter */
.link-preview:not(:has(.link-preview-image)) .link-preview-content {
    padding: 16px;
}

/* Focus states voor accessibility */
.link-preview-card:focus {
    outline: 2px solid #1da1f2;
    outline-offset: 2px;
}

/* Loading state (optioneel) */
.link-preview.loading {
    opacity: 0.7;
    pointer-events: none;
}

.link-preview.loading .link-preview-image {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% {
        background-position: 200% 0;
    }
    100% {
        background-position: -200% 0;
    }
}
</style>