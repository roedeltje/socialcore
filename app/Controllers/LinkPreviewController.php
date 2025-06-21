<?php

namespace App\Controllers;

use App\Database\Database;
use PDO;
use Exception;

class LinkPreviewController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getPdo();

        // Custom error log
        ini_set('log_errors', 1);
        ini_set('error_log', '/var/www/socialcore.local/debug_linkpreview.log');
    }

    /**
     * API endpoint om preview data te genereren voor een URL
     * Route: /linkpreview/generate
     */
    public function generate()
    {
        $debugFile = '/var/www/socialcore.local/debug/linkpreview_debug_' . date('Y-m-d') . '.log';
        
        file_put_contents($debugFile, 
            "[" . date('Y-m-d H:i:s') . "] === LINKPREVIEW GENERATE CALLED ===\n", 
            FILE_APPEND | LOCK_EX);

        header('Content-Type: application/json');
        
        try {
            // Check login
            if (!isset($_SESSION['user_id'])) {
                file_put_contents($debugFile, "ERROR: User not logged in\n", FILE_APPEND | LOCK_EX);
                echo json_encode(['error' => 'Unauthorized']);
                return;
            }
            file_put_contents($debugFile, "✅ User logged in: " . $_SESSION['user_id'] . "\n", FILE_APPEND | LOCK_EX);

            // Get URL
            $url = $_POST['url'] ?? $_GET['url'] ?? '';
            if (empty($url)) {
                file_put_contents($debugFile, "ERROR: No URL\n", FILE_APPEND | LOCK_EX);
                echo json_encode(['error' => 'URL parameter required']);
                return;
            }
            file_put_contents($debugFile, "✅ Got URL: " . $url . "\n", FILE_APPEND | LOCK_EX);

            // Validate URL
            file_put_contents($debugFile, "🔍 Validating URL...\n", FILE_APPEND | LOCK_EX);
            if (!$this->isValidUrl($url)) {
                file_put_contents($debugFile, "ERROR: Invalid URL\n", FILE_APPEND | LOCK_EX);
                echo json_encode(['error' => 'Invalid URL format']);
                return;
            }
            file_put_contents($debugFile, "✅ URL is valid\n", FILE_APPEND | LOCK_EX);

            // Check cache
            file_put_contents($debugFile, "🔍 Checking cache...\n", FILE_APPEND | LOCK_EX);
            $cachedPreview = $this->getCachedPreview($url);
            
            if ($cachedPreview) {
                file_put_contents($debugFile, "✅ Found in cache\n", FILE_APPEND | LOCK_EX);
                echo json_encode([
                    'success' => true,
                    'preview' => $cachedPreview,
                    'cached' => true
                ]);
                return;
            }
            file_put_contents($debugFile, "ℹ️ Not in cache, generating...\n", FILE_APPEND | LOCK_EX);

            // Generate preview
            file_put_contents($debugFile, "🔍 Calling generatePreview()...\n", FILE_APPEND | LOCK_EX);
            $previewData = $this->generatePreview($url);
            file_put_contents($debugFile, "✅ generatePreview() returned: " . ($previewData ? 'SUCCESS' : 'FAILED') . "\n", FILE_APPEND | LOCK_EX);
            
            if ($previewData) {
                file_put_contents($debugFile, "🔍 Caching preview...\n", FILE_APPEND | LOCK_EX);
                $previewId = $this->cachePreview($url, $previewData);
                $previewData['id'] = $previewId;
                
                file_put_contents($debugFile, "✅ SUCCESS - sending response\n", FILE_APPEND | LOCK_EX);
                echo json_encode([
                    'success' => true,
                    'preview' => $previewData,
                    'cached' => false
                ]);
            } else {
                file_put_contents($debugFile, "❌ Could not generate preview\n", FILE_APPEND | LOCK_EX);
                echo json_encode([
                    'success' => false,
                    'error' => 'Could not generate preview for this URL'
                ]);
            }

        } catch (Exception $e) {
            file_put_contents($debugFile, "💥 EXCEPTION: " . $e->getMessage() . "\n", FILE_APPEND | LOCK_EX);
            file_put_contents($debugFile, "Stack: " . $e->getTraceAsString() . "\n", FILE_APPEND | LOCK_EX);
            echo json_encode(['success' => false, 'error' => 'Server error']);
        }
    }

    /**
     * Valideer of URL geldig is
     */
    private function isValidUrl($url)
    {
        // Check basic URL format
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        // Check of het HTTP/HTTPS is
        $parsed = parse_url($url);
        if (!in_array($parsed['scheme'] ?? '', ['http', 'https'])) {
            return false;
        }

        // Check of domain niet op blacklist staat
        if ($this->isDomainBlocked($parsed['host'] ?? '')) {
            return false;
        }

        return true;
    }

    /**
     * Check of domain geblokkeerd is
     */
    private function isDomainBlocked($domain)
    {
        // Voor nu simpele blacklist check
        $blockedDomains = [
            'malware-site.com',
            'spam-site.net'
            // Meer domains kunnen later worden toegevoegd
        ];

        return in_array($domain, $blockedDomains);
    }

    /**
     * Haal cached preview op uit database
     */
    private function getCachedPreview($url)
    {
        $stmt = $this->db->prepare("
            SELECT id, title, description, image_url, domain, created_at 
            FROM link_previews 
            WHERE url = ? AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)
        ");
        $stmt->execute([$url]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            return [
                'id' => $result['id'],
                'url' => $url,
                'title' => $result['title'],
                'description' => $result['description'],
                'image_url' => $result['image_url'],
                'domain' => $result['domain'],
                'created_at' => $result['created_at']
            ];
        }

        return null;
    }

    /**
     * Genereer nieuwe preview door website te scrapen
     */
    private function generatePreview($url)
    {
        $debugFile = '/var/www/socialcore.local/debug/linkpreview_debug_' . date('Y-m-d') . '.log';
        
        file_put_contents($debugFile, "🔧 generatePreview() START for: $url\n", FILE_APPEND | LOCK_EX);
        
        // Basic metadata die we willen ophalen
        $previewData = [
            'url' => $url,
            'title' => '',
            'description' => '',
            'image_url' => '',
            'domain' => parse_url($url, PHP_URL_HOST) ?? ''
        ];
        file_put_contents($debugFile, "✅ Preview data initialized\n", FILE_APPEND | LOCK_EX);

        try {
            // Haal website content op met cURL
            file_put_contents($debugFile, "🔍 Calling fetchWebsiteContent()...\n", FILE_APPEND | LOCK_EX);
            $html = $this->fetchWebsiteContent($url);
            file_put_contents($debugFile, "✅ fetchWebsiteContent returned: " . strlen($html ?? '') . " chars\n", FILE_APPEND | LOCK_EX);
            
            if (!$html) {
                file_put_contents($debugFile, "❌ No HTML content - using fallback\n", FILE_APPEND | LOCK_EX);
                return $this->generateFallbackPreview($url, $previewData);
            }

            // Parse HTML voor metadata
            file_put_contents($debugFile, "🔍 Extracting title...\n", FILE_APPEND | LOCK_EX);
            $previewData['title'] = $this->extractTitle($html, $url);
            file_put_contents($debugFile, "✅ Title: " . $previewData['title'] . "\n", FILE_APPEND | LOCK_EX);
            
            file_put_contents($debugFile, "🔍 Extracting description...\n", FILE_APPEND | LOCK_EX);
            $previewData['description'] = $this->extractDescription($html);
            file_put_contents($debugFile, "✅ Description: " . substr($previewData['description'], 0, 50) . "...\n", FILE_APPEND | LOCK_EX);
            
            file_put_contents($debugFile, "🔍 Extracting image...\n", FILE_APPEND | LOCK_EX);
            $previewData['image_url'] = $this->extractImage($html, $url);
            file_put_contents($debugFile, "✅ Image: " . $previewData['image_url'] . "\n", FILE_APPEND | LOCK_EX);

            // Check of het een Nederlandse site is (met www. stripping)
            $dutchSites = ['rtl.nl', 'nos.nl', 'nu.nl', 'ad.nl'];
            $cleanDomain = str_replace('www.', '', $previewData['domain']);
            $isDutchSite = in_array($cleanDomain, $dutchSites);

            file_put_contents($debugFile, "🔍 Checking if $cleanDomain is Dutch site...\n", FILE_APPEND | LOCK_EX);

            // Voor Nederlandse sites: gebruik altijd fallback afbeelding
            if ($isDutchSite) {
                file_put_contents($debugFile, "🇳🇱 Dutch site detected - applying FULL fallback...\n", FILE_APPEND | LOCK_EX);
                $fallbackData = $this->generateFallbackPreview($url, $previewData);
                
                // Overschrijf ALLES met fallback data
                $previewData['title'] = $fallbackData['title'];
                $previewData['description'] = $fallbackData['description'];
                $previewData['image_url'] = $fallbackData['image_url'];
                
                file_put_contents($debugFile, "✅ Full Dutch fallback applied - Title: " . $previewData['title'] . "\n", FILE_APPEND | LOCK_EX);
                file_put_contents($debugFile, "✅ Full Dutch fallback applied - Image: " . $previewData['image_url'] . "\n", FILE_APPEND | LOCK_EX);
            }
            
            // Reguliere fallback voor sites zonder titel/beschrijving
            $needsRegularFallback = empty($previewData['title']) && empty($previewData['description']);
            
            if ($needsRegularFallback) {
                file_put_contents($debugFile, "🔄 Using regular fallback for missing metadata...\n", FILE_APPEND | LOCK_EX);
                $fallbackData = $this->generateFallbackPreview($url, $previewData);
                
                // Behoud gevonden data, vul aan met fallback
                $previewData['title'] = $previewData['title'] ?: $fallbackData['title'];
                $previewData['description'] = $previewData['description'] ?: $fallbackData['description'];
                $previewData['image_url'] = $previewData['image_url'] ?: $fallbackData['image_url'];
                
                file_put_contents($debugFile, "✅ Regular fallback applied - Title: " . $previewData['title'] . "\n", FILE_APPEND | LOCK_EX);
            }

            file_put_contents($debugFile, "✅ generatePreview() SUCCESS\n", FILE_APPEND | LOCK_EX);
            return $previewData;

        } catch (Exception $e) {
            file_put_contents($debugFile, "💥 generatePreview() EXCEPTION: " . $e->getMessage() . "\n", FILE_APPEND | LOCK_EX);
            error_log('Preview generation failed for ' . $url . ': ' . $e->getMessage());
            return $this->generateFallbackPreview($url, $previewData);
        }
    }

    /**
     * Genereer titel van URL path als fallback
     */
    private function generateTitleFromPath($path, $domain)
    {
        if (!$path || $path === '/') {
            return ucfirst(str_replace(['.nl', '.com'], '', $domain));
        }
        
        // Haal laatste segment van path
        $segments = explode('/', trim($path, '/'));
        $lastSegment = end($segments);
        
        // Vervang hyphens en underscores met spaties
        $title = str_replace(['-', '_'], ' ', $lastSegment);
        
        // Capitalize words
        return ucwords($title);
    }

    /**
     * Genereer fallback beschrijving
     */
    private function generateFallbackDescription($domain)
    {
        $descriptions = [
            'nos.nl' => 'Nieuws, sport en achtergronden van Nederland en de wereld',
            'nu.nl' => 'Het laatste nieuws uit Nederland en de wereld',
            'ad.nl' => 'Nieuws uit Nederland en de regio',
            'telegraaf.nl' => 'Het laatste nieuws uit Nederland en de wereld'
        ];
        
        return $descriptions[$domain] ?? "Bekijk deze pagina op $domain";
    }

     /**
     * Genereer fallback preview voor problematische sites
     */
    private function generateFallbackPreview($url, $previewData)
    {
        $debugFile = '/var/www/socialcore.local/debug/linkpreview_debug_' . date('Y-m-d') . '.log';

        
        $domain = $previewData['domain'];

        $cleanDomain = str_replace('www.', '', $domain);

        file_put_contents($debugFile, "🔧 Fallback for domain: $domain\n", FILE_APPEND | LOCK_EX);
        
        // Nederlandse sites met default afbeeldingen
        $dutchSiteData = [
            'rtl.nl' => [
                'title' => 'RTL Nieuws',
                'description' => 'Het laatste nieuws uit Nederland en de wereld',
                'image_url' => base_url('assets/images/link-previews/rtl-logo.png') // ← LET OP: image_url
            ],
            'nos.nl' => [
                'title' => 'NOS Nieuws', 
                'description' => 'Nieuws, sport en achtergronden',
                'image_url' => base_url('assets/images/link-previews/nos-logo.png') // ← image_url
            ],
            'nu.nl' => [
                'title' => 'NU.nl - Het laatste nieuws',
                'description' => 'Het laatste nieuws uit Nederland en de wereld',
                'image_url' => base_url('assets/images/link-previews/nu-logo.png') // ← image_url
            ],
            'ad.nl' => [
                'title' => 'Algemeen Dagblad',
                'description' => 'Nieuws uit Nederland en de regio',
                'image_url' => base_url('assets/images/link-previews/ad-logo.png') // ← image_url
            ]
        ];
        
        if (isset($dutchSiteData[$cleanDomain])) {
            $siteData = $dutchSiteData[$cleanDomain];
            $previewData['title'] = $siteData['title'];
            $previewData['description'] = $siteData['description'];
            $previewData['image_url'] = $siteData['image_url'];
            
            file_put_contents($debugFile, "✅ Site data found for $cleanDomain\n", FILE_APPEND | LOCK_EX);
            file_put_contents($debugFile, "✅ Image URL set to: " . $siteData['image_url'] . "\n", FILE_APPEND | LOCK_EX);
        } else {
            // Fallback voor onbekende sites
            $previewData['title'] = ucfirst(str_replace('.nl', '', $domain));
            $previewData['description'] = "Bekijk deze pagina op " . $domain;
            
            // Probeer titel uit URL te halen
            $path = parse_url($url, PHP_URL_PATH);
            if ($path && strpos($path, '/artikel/') !== false) {
                $segments = explode('/', $path);
                $lastSegment = end($segments);
                if ($lastSegment && strlen($lastSegment) > 10) {
                    $urlTitle = str_replace(['-', '_'], ' ', $lastSegment);
                    $urlTitle = ucwords($urlTitle);
                    $previewData['title'] = $urlTitle;
                }
            }
            
            file_put_contents($debugFile, "ℹ️ No specific data for $domain, using generic fallback\n", FILE_APPEND | LOCK_EX);
        }
        
        return $previewData;
    }

    /**
     * Haal website content op met cURL
     */
    private function fetchWebsiteContent($url)
    {
        $debugFile = '/var/www/socialcore.local/debug/linkpreview_debug_' . date('Y-m-d') . '.log';
        
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_CONNECTTIMEOUT => 8,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING => '', // ← BELANGRIJKE FIX: Auto-detecteer en decompress GZIP/deflate
            CURLOPT_HTTPHEADER => [
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
                'Accept-Language: en-US,en;q=0.9',
                'Accept-Encoding: gzip, deflate, br', // ← Zeg dat we GZIP aankunnen
                'Connection: keep-alive',
                'Upgrade-Insecure-Requests: 1',
                'Cache-Control: no-cache'
            ]
        ]);

        $content = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        $error = curl_error($ch);
        
        curl_close($ch);

        // Debug info
        file_put_contents($debugFile, "🌐 HTTP Code: $httpCode\n", FILE_APPEND | LOCK_EX);
        file_put_contents($debugFile, "📄 Content-Type: $contentType\n", FILE_APPEND | LOCK_EX);
        
        if ($error) {
            file_put_contents($debugFile, "❌ cURL Error: $error\n", FILE_APPEND | LOCK_EX);
        }

        // DEBUG: Log eerste 200 karakters van gedecomprimeerde content
        if ($content && strpos($url, 'youtube.com') !== false) {
            file_put_contents($debugFile, "🔍 YouTube HTML (decompressed, first 200 chars):\n", FILE_APPEND | LOCK_EX);
            file_put_contents($debugFile, substr($content, 0, 200) . "\n", FILE_APPEND | LOCK_EX);
            file_put_contents($debugFile, "====================\n", FILE_APPEND | LOCK_EX);
        }

        // Check of request succesvol was
        if ($httpCode !== 200 || $content === false) {
            file_put_contents($debugFile, "❌ Failed: HTTP $httpCode or no content\n", FILE_APPEND | LOCK_EX);
            return null;
        }
        
        // Check of het HTML is
        if (!empty($contentType) && !str_contains($contentType, 'text/html')) {
            file_put_contents($debugFile, "❌ Not HTML: $contentType\n", FILE_APPEND | LOCK_EX);
            return null;
        }

        return $content;
    }

    /**
     * Detecteer Nederlandse websites
     */
    private function isDutchSite($url)
    {
        $dutchDomains = [
            'nos.nl', 'nu.nl', 'ad.nl', 'telegraaf.nl', 'volkskrant.nl', 
            'nrc.nl', 'rtl.nl', 'sbs6.nl', 'omroepwest.nl', 'rtvnoord.nl',
            'government.nl', 'rijksoverheid.nl', 'gemeenten.nl'
        ];
        
        $host = parse_url($url, PHP_URL_HOST);
        
        foreach ($dutchDomains as $domain) {
            if (str_ends_with($host, $domain)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Haal titel uit HTML
     */
    private function extractTitle($html, $url)
    {
        $debugFile = '/var/www/socialcore.local/debug/linkpreview_debug_' . date('Y-m-d') . '.log';
    
    // DEBUG: Zoek naar Open Graph title tags in de HTML
    if (strpos($url, 'youtube.com') !== false) {
        file_put_contents($debugFile, "🔍 Searching for YouTube title patterns...\n", FILE_APPEND | LOCK_EX);
        
        // Test alle mogelijke patterns
        $testPatterns = [
            'og:title' => '/<meta property="og:title" content="([^"]*)"[^>]*>/i',
            'title tag' => '/<title[^>]*>([^<]*)<\/title>/i',
            'name title' => '/<meta name="title" content="([^"]*)"[^>]*>/i'
        ];
        
        foreach ($testPatterns as $name => $pattern) {
            if (preg_match($pattern, $html, $matches)) {
                file_put_contents($debugFile, "✅ Found $name: " . $matches[1] . "\n", FILE_APPEND | LOCK_EX);
            } else {
                file_put_contents($debugFile, "❌ No match for $name\n", FILE_APPEND | LOCK_EX);
            }
        }
        
        // Zoek specifiek naar YouTube title in de eerste 5000 karakters
        $htmlStart = substr($html, 0, 5000);
        if (preg_match('/<title[^>]*>([^<]*)<\/title>/i', $htmlStart, $matches)) {
            file_put_contents($debugFile, "✅ Title in first 5000 chars: " . $matches[1] . "\n", FILE_APPEND | LOCK_EX);
        }
        
        // Zoek naar alle meta tags
        preg_match_all('/<meta[^>]*property="og:title"[^>]*>/i', $html, $ogTags);
        file_put_contents($debugFile, "🔍 Found " . count($ogTags[0]) . " og:title tags\n", FILE_APPEND | LOCK_EX);
        
        if (count($ogTags[0]) > 0) {
            file_put_contents($debugFile, "First og:title tag: " . $ogTags[0][0] . "\n", FILE_APPEND | LOCK_EX);
        }
    }
        
        // Probeer verschillende methoden voor Nederlandse sites
        $methods = [
            // RTL specifiek
            '/<h1[^>]*class="[^"]*article[^"]*"[^>]*>([^<]+)<\/h1>/i',
            '/<h1[^>]*>([^<]+)<\/h1>/i',
            // Open Graph (standaard)
            '/<meta property="og:title" content="([^"]*)"[^>]*>/i',
            // Twitter Card
            '/<meta name="twitter:title" content="([^"]*)"[^>]*>/i',
            // JSON-LD structured data (veel Nederlandse sites)
            '/"headline":\s*"([^"]*)"/',
            // Title tag als laatste
            '/<title[^>]*>([^<]*)<\/title>/i'
        ];
        
        foreach ($methods as $index => $pattern) {
            if (preg_match($pattern, $html, $matches)) {
                $title = html_entity_decode(trim($matches[1]), ENT_QUOTES, 'UTF-8');
                
                // Clean up titel (verwijder site naam)
                $title = $this->cleanTitle($title, $url);
                
                if (!empty($title) && strlen($title) > 3) {
                    file_put_contents($debugFile, "✅ Title found with method $index: $title\n", FILE_APPEND | LOCK_EX);
                    return $title;
                }
            }
        }
        
        file_put_contents($debugFile, "❌ No title found\n", FILE_APPEND | LOCK_EX);
        return '';
    }

     /**
     * Clean up titel voor Nederlandse sites
     */
    private function cleanTitle($title, $url)
    {
        $domain = parse_url($url, PHP_URL_HOST);
        
        // Verwijder site namen van titels
        $siteNames = [
            'RTL Nieuws' => '',
            '| RTL Nieuws' => '',
            '- RTL Nieuws' => '',
            'NOS' => '',
            '| NOS' => '',
            '- NOS' => '',
            'NU.nl' => '',
            '| NU.nl' => '',
            '- NU.nl' => ''
        ];
        
        foreach ($siteNames as $siteName => $replacement) {
            $title = str_ireplace($siteName, $replacement, $title);
        }
        
        return trim($title);
    }

    /**
     * Haal beschrijving uit HTML
     */
    private function extractDescription($html)
    {
        $patterns = [
            // Open Graph
            '/<meta property="og:description" content="([^"]*)"[^>]*>/i',
            // Twitter Card  
            '/<meta name="twitter:description" content="([^"]*)"[^>]*>/i',
            // Standard meta description
            '/<meta name="description" content="([^"]*)"[^>]*>/i',
            // JSON-LD
            '/"description":\s*"([^"]*)"/',
            // Article lead/intro paragraphs
            '/<p[^>]*class="[^"]*lead[^"]*"[^>]*>([^<]+)<\/p>/i',
            '/<p[^>]*class="[^"]*intro[^"]*"[^>]*>([^<]+)<\/p>/i'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $html, $matches)) {
                $description = html_entity_decode(trim($matches[1]), ENT_QUOTES, 'UTF-8');
                if (!empty($description) && strlen($description) > 10) {
                    return $description;
                }
            }
        }
        
        return '';
    }

    /**
     * Haal afbeelding URL uit HTML
     */
    private function extractImage($html, $url)
    {
        $debugFile = '/var/www/socialcore.local/debug/linkpreview_debug_' . date('Y-m-d') . '.log';

        // TIJDELIJKE DEBUG: Zoek naar alle img tags
    if (strpos($url, 'rtl.nl') !== false) {
        preg_match_all('/<img[^>]*>/i', $html, $allImages);
        file_put_contents($debugFile, "🔍 RTL IMG tags found: " . count($allImages[0]) . "\n", FILE_APPEND | LOCK_EX);
        
        // Log eerste 3 img tags
        for ($i = 0; $i < min(3, count($allImages[0])); $i++) {
            file_put_contents($debugFile, "IMG $i: " . $allImages[0][$i] . "\n", FILE_APPEND | LOCK_EX);
        }
        
        // Zoek ook naar JSON-LD data
        if (preg_match('/"image"[^}]+/', $html, $jsonMatch)) {
            file_put_contents($debugFile, "JSON-LD image data: " . $jsonMatch[0] . "\n", FILE_APPEND | LOCK_EX);
        }
    }
        
        $patterns = [
            // Open Graph
            '/<meta property="og:image" content="([^"]*)"[^>]*>/i',
            // Twitter Card
            '/<meta name="twitter:image" content="([^"]*)"[^>]*>/i',
            // RTL specifieke patronen
            '/<img[^>]*class="[^"]*article[^"]*"[^>]*src="([^"]*)"[^>]*>/i',
            '/<img[^>]*src="([^"]*)"[^>]*class="[^"]*article[^"]*"[^>]*>/i',
            // JSON-LD structured data
            '/"image":\s*"([^"]*)"/',
            '/"image":\s*\[\s*"([^"]*)"/',
            // Algemene img tags in article content
            '/<article[^>]*>.*?<img[^>]*src="([^"]*)"[^>]*>.*?<\/article>/si',
            '/<div[^>]*class="[^"]*content[^"]*"[^>]*>.*?<img[^>]*src="([^"]*)"[^>]*>.*?<\/div>/si'
        ];
        
        foreach ($patterns as $index => $pattern) {
            if (preg_match($pattern, $html, $matches)) {
                $imageUrl = trim($matches[1]);
                
                // Convert relative URLs to absolute
                $absoluteUrl = $this->resolveImageUrl($imageUrl, $url);
                
                // Valideer of het een echte afbeelding is
                if ($this->isValidImageUrl($absoluteUrl)) {
                    file_put_contents($debugFile, "✅ Image found with method $index: $absoluteUrl\n", FILE_APPEND | LOCK_EX);
                    return $absoluteUrl;
                }
            }
        }
        
        file_put_contents($debugFile, "❌ No image found\n", FILE_APPEND | LOCK_EX);
        return '';
    }

    /**
     * Valideer of URL een echte afbeelding is
     */
    private function isValidImageUrl($url)
    {
        if (empty($url)) return false;
        
        // Check bestandsextensie
        $extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        $extension = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
        
        if (in_array($extension, $extensions)) {
            return true;
        }
        
        // Check of URL afbeelding parameters bevat
        if (strpos($url, 'image') !== false || 
            strpos($url, 'photo') !== false || 
            strpos($url, 'picture') !== false) {
            return true;
        }
        
        return false;
    }

    /**
     * Zet relatieve image URL om naar absolute URL
     */
    private function resolveImageUrl($imageUrl, $baseUrl)
    {
        // Als het al een absolute URL is, return as-is
        if (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            return $imageUrl;
        }

        // Parse base URL
        $parsedBase = parse_url($baseUrl);
        $scheme = $parsedBase['scheme'] ?? 'https';
        $host = $parsedBase['host'] ?? '';

        // Als image URL begint met //, voeg scheme toe
        if (strpos($imageUrl, '//') === 0) {
            return $scheme . ':' . $imageUrl;
        }

        // Als image URL begint met /, maak absolute URL
        if (strpos($imageUrl, '/') === 0) {
            return $scheme . '://' . $host . $imageUrl;
        }

        // Anders, voeg toe aan base path
        $basePath = dirname($parsedBase['path'] ?? '/');
        return $scheme . '://' . $host . rtrim($basePath, '/') . '/' . $imageUrl;
    }

    /**
     * Sla preview op in cache database
     */
    private function cachePreview($url, $previewData)
    {
        $stmt = $this->db->prepare("
            INSERT INTO link_previews (url, title, description, image_url, domain, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        
        $stmt->execute([
            $url,
            $previewData['title'],
            $previewData['description'],
            $previewData['image_url'],
            $previewData['domain']
        ]);

        return $this->db->lastInsertId();
    }
}