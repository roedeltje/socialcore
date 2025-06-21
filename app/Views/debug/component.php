<?php
// /app/Views/debug/component.php
// Component System Debug View
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <style>
        * { box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
            margin: 0; 
            padding: 20px; 
            background: #f5f5f5; 
            line-height: 1.6;
        }
        .container { max-width: 1200px; margin: 0 auto; }
        .debug-header { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            color: white; 
            padding: 20px; 
            border-radius: 10px; 
            margin-bottom: 20px; 
        }
        .debug-header h1 { margin: 0; font-size: 28px; }
        .debug-header p { margin: 5px 0 0 0; opacity: 0.9; }
        .debug-box { 
            background: white; 
            padding: 20px; 
            margin: 15px 0; 
            border-radius: 8px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
        }
        .debug-box h2 { 
            margin: 0 0 15px 0; 
            color: #333; 
            font-size: 20px; 
            border-bottom: 2px solid #eee; 
            padding-bottom: 8px; 
        }
        .success { border-left: 4px solid #28a745; background: #f8fff9; }
        .error { border-left: 4px solid #dc3545; background: #fff8f8; }
        .warning { border-left: 4px solid #ffc107; background: #fffbf0; }
        .info { border-left: 4px solid #17a2b8; background: #f0fcff; }
        
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
        .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        
        pre { 
            background: #2d3748; 
            color: #e2e8f0; 
            padding: 15px; 
            border-radius: 6px; 
            overflow-x: auto; 
            font-size: 12px; 
            max-height: 300px; 
            overflow-y: auto; 
        }
        
        .component-test { 
            border: 1px solid #ddd; 
            border-radius: 6px; 
            margin: 10px 0; 
        }
        .component-header { 
            background: #f8f9fa; 
            padding: 10px 15px; 
            border-bottom: 1px solid #ddd; 
            font-weight: bold; 
        }
        .component-body { padding: 15px; }
        .component-output { 
            background: #f8f9fa; 
            border: 1px solid #ddd; 
            border-radius: 4px; 
            padding: 10px; 
            margin-top: 10px; 
        }
        
        .status-badge { 
            display: inline-block; 
            padding: 4px 8px; 
            border-radius: 12px; 
            font-size: 11px; 
            font-weight: bold; 
            text-transform: uppercase; 
        }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-error { background: #f8d7da; color: #721c24; }
        .badge-warning { background: #fff3cd; color: #856404; }
        
        .nav-links { margin-bottom: 20px; }
        .nav-links a { 
            display: inline-block; 
            padding: 8px 16px; 
            background: #6c757d; 
            color: white; 
            text-decoration: none; 
            border-radius: 4px; 
            margin-right: 10px; 
            font-size: 14px; 
        }
        .nav-links a:hover { background: #5a6268; }
        .nav-links a.active { background: #007bff; }
        
        .path-list { 
            background: #f8f9fa; 
            border: 1px solid #dee2e6; 
            border-radius: 4px; 
            padding: 10px; 
        }
        .path-list li { 
            padding: 5px 0; 
            font-family: monospace; 
            font-size: 12px; 
            list-style: none;
        }
        .path-exists { color: #28a745; font-weight: bold; }
        .path-missing { color: #dc3545; }
        
        .text-muted { color: #6c757d; font-style: italic; }
    </style>
</head>
<body>
    <div class="container">
        <div class="debug-header">
            <h1>🧪 <?= $page_title ?></h1>
            <p>Test en analyseer het theme component systeem</p>
        </div>
        
        <div class="nav-links">
            <a href="?route=debug" class="active">🏠 Debug Home</a>
            <a href="?route=debug/theme">🎨 Theme System</a>
            <a href="?route=debug/database">🗄️ Database</a>
            <a href="?route=debug/session">👤 Session</a>
            <a href="?route=debug/routes">🛣️ Routes</a>
            <a href="?route=debug/performance">⚡ Performance</a>
        </div>
        
        <div class="two-col">
            <!-- Basis Info -->
            <div class="debug-box info">
                <h2>📋 Systeem Informatie</h2>
                <p><strong>Actief Thema:</strong> <code><?= $active_theme ?></code></p>
                <p><strong>Beschikbare Thema's:</strong> 
                    <?php foreach ($available_themes as $slug => $theme): ?>
                        <span class="status-badge <?= $slug === $active_theme ? 'badge-success' : 'badge-warning' ?>">
                            <?= $theme['name'] ?? $slug ?>
                        </span>
                    <?php endforeach; ?>
                </p>
                <p><strong>Components gevonden:</strong> <?= count($components) ?></p>
                <p><strong>Theme ondersteuning:</strong> 
                    <?= !empty($theme_support['features']) ? count($theme_support['features']) . ' features' : 'Geen features' ?>
                </p>
            </div>
            
            <!-- Theme Support -->
            <div class="debug-box">
                <h2>🎯 Theme Ondersteuning</h2>
                <?php if (!empty($theme_support['features'])): ?>
                    <p><strong>Features:</strong></p>
                    <ul>
                        <?php foreach ($theme_support['features'] as $feature => $value): ?>
                            <li><code><?= $feature ?></code>: 
                                <span class="status-badge <?= $value ? 'badge-success' : 'badge-error' ?>">
                                    <?= $value ? 'Ja' : 'Nee' ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted">Geen theme features gedefinieerd</p>
                <?php endif; ?>
                
                <?php if (!empty($theme_support['component_list'])): ?>
                    <p><strong>Beschikbare Components:</strong></p>
                    <div style="font-family: monospace; font-size: 12px;">
                        <?php foreach ($theme_support['component_list'] as $comp): ?>
                            <span class="status-badge badge-success"><?= $comp ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Component Tests -->
        <div class="debug-box">
            <h2>🧪 Component Tests</h2>
            <p>Test verschillende components met dummy data om te zien of ze correct laden:</p>
            
            <?php foreach ($test_components as $component_name => $component_info): ?>
                <div class="component-test">
                    <div class="component-header">
                        <?= $component_info['name'] ?> (<code><?= $component_name ?></code>)
                        <?php 
                        $exists = theme_component_exists($component_name);
                        $debug_info = debug_component_loading($component_name);
                        ?>
                        <span class="status-badge <?= $exists ? 'badge-success' : 'badge-error' ?>" style="float: right;">
                            <?= $exists ? '✅ Gevonden' : '❌ Niet gevonden' ?>
                        </span>
                    </div>
                    <div class="component-body">
                        <?php if ($exists): ?>
                            <p><strong>Geladen van:</strong> <code><?= $debug_info['found_path'] ?></code></p>
                            
                            <div class="component-output">
                                <strong>Component Output:</strong>
                                <div style="margin-top: 10px; border: 1px solid #ddd; padding: 10px; background: white;">
                                    <?php get_theme_component($component_name, $component_info['data']); ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <p><strong>❌ Component niet gevonden</strong></p>
                            <p><strong>Gezochte locaties:</strong></p>
                            <ul class="path-list">
                                <?php foreach ($debug_info['searched_paths'] as $type => $path_info): ?>
                                    <li class="<?= $path_info['exists'] ? 'path-exists' : 'path-missing' ?>">
                                        <?= $path_info['exists'] ? '✅' : '❌' ?> <?= $path_info['path'] ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Debug Details -->
        <div class="debug-box">
            <h2>🔍 Gedetailleerde Debug Info</h2>
            <p>Uitgebreide informatie over het component systeem:</p>
            
            <h3>Theme Configuration:</h3>
            <pre><?php var_dump($theme_support); ?></pre>
            
            <h3>Alle Components:</h3>
            <pre><?php var_dump($components); ?></pre>
            
            <h3>Available Themes:</h3>
            <pre><?php var_dump($available_themes); ?></pre>
        </div>
        
        <div class="debug-box">
            <h2>🔧 Snelle Acties</h2>
            <p>
                <a href="?route=debug" style="color: #007bff;">← Terug naar Debug Home</a> | 
                <a href="<?= base_url() ?>" style="color: #007bff;">🏠 Naar Homepage</a> | 
                <a href="?route=debug/theme" style="color: #007bff;">🎨 Theme Debug</a>
            </p>
            <p><small>🛠️ Deze debug pagina is alleen beschikbaar voor administrators</small></p>
        </div>
    </div>
</body>
</html>