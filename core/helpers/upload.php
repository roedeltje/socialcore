<?php
/**
 * Helper functies voor bestandsuploads
 */

    /**
     * Genereert een unieke bestandsnaam voor uploads
     *
     * @param string $originalName Originele bestandsnaam
     * @param string $prefix Optionele prefix voor de bestandsnaam
     * @return string Unieke bestandsnaam
     */
    function generate_unique_filename($originalName, $prefix = '') {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $uniqueId = uniqid($prefix, true);
        $uniqueName = $uniqueId . '.' . strtolower($extension);
        return $uniqueName;
    }

    /**
     * Bepaalt het pad voor een upload op basis van type
     *
     * @param string $type Type upload (avatars, covers, posts, attachments)
     * @param string $customPath Optioneel aangepast pad
     * @return string Volledig pad voor upload
     */
    function get_upload_path($type = 'posts', $customPath = '') {
        if ($customPath) {
            return BASE_PATH . '/public/uploads/' . $type . '/' . $customPath;
        }
        
        $year = date('Y');
        $month = date('m');
        
        // Controleer of de directory bestaat, zo niet, maak deze aan
        $uploadDir = BASE_PATH . '/public/uploads/' . $type . '/' . $year . '/' . $month;
        
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        return $uploadDir;
    }

    /**
     * Uploadt een bestand en retourneert het relatieve pad
     *
     * @param array $file $_FILES array element
     * @param string $type Type upload (avatars, covers, posts, attachments)
     * @param array $allowedTypes Array met toegestane MIME types
     * @param int $maxSize Maximale bestandsgrootte in bytes (default 5MB)
     * @param string $prefix Optionele prefix voor bestandsnaam
     * @return array Resultaat van upload [success, message, path]
     */
    function upload_file($file, $type = 'posts', $allowedTypes = [], $maxSize = 5242880, $prefix = '') {
        // Debug om te zien wat er binnenkomt
        error_log('Upload bestand: ' . ($file['name'] ?? 'geen') . ', type: ' . $type);
        
        // Controleer of er een bestand is geüpload
        if (!isset($file) || $file['error'] != UPLOAD_ERR_OK) {
            return [
                'success' => false,
                'message' => 'Er is een fout opgetreden bij het uploaden: ' . upload_error_message($file['error']),
                'path' => null
            ];
        }
        
        // Controleer bestandsgrootte
        if ($file['size'] > $maxSize) {
            return [
                'success' => false,
                'message' => 'Het bestand is te groot (max ' . format_filesize($maxSize) . ')',
                'path' => null
            ];
        }
        
        // Controleer bestandstype als er toegestane types zijn opgegeven
        if (!empty($allowedTypes)) {
            $fileMimeType = mime_content_type($file['tmp_name']);
            if (!in_array($fileMimeType, $allowedTypes)) {
                return [
                    'success' => false,
                    'message' => 'Bestandstype niet toegestaan',
                    'path' => null
                ];
            }
        }
        
        // Genereer unieke bestandsnaam
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $uniqueId = uniqid($prefix, true);
        $filename = $uniqueId . '.' . strtolower($extension);
        
        // Bepaal upload pad
        $year = date('Y');
        $month = date('m');
        $relativePath = $type . '/' . $year . '/' . $month . '/' . $filename;
        $uploadDir = BASE_PATH . '/public/uploads/' . $type . '/' . $year . '/' . $month;
        $uploadPath = $uploadDir . '/' . $filename;
        
        // Controleer of de directory bestaat, zo niet, maak deze aan
        if (!is_dir($uploadDir)) {
            error_log('Maak directory aan: ' . $uploadDir);
            mkdir($uploadDir, 0755, true);
        }
        
        // Upload het bestand
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            error_log('Upload success: ' . $uploadPath);
            error_log('Relatief pad: ' . $relativePath);
            
            return [
                'success' => true,
                'message' => 'Bestand succesvol geüpload',
                'path' => $relativePath,
                'full_path' => $uploadPath
            ];
        } else {
            $error = error_get_last();
            $errorMsg = $error ? $error['message'] : 'Onbekende fout';
            error_log('Upload failed: ' . $errorMsg);
            
            return [
                'success' => false,
                'message' => 'Fout bij het opslaan van het bestand: ' . $errorMsg,
                'path' => null
            ];
        }
    }

    function create_thumbnail($sourcePath, $destPath = null, $maxWidth = 150, $maxHeight = 150) {
    if (!file_exists($sourcePath)) {
        return false;
    }
    
    // Als geen destination path is opgegeven, maak een thumbnail naam
    if (!$destPath) {
        $pathInfo = pathinfo($sourcePath);
        $destPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_thumb.' . $pathInfo['extension'];
    }
    
    // Haal afbeelding informatie op
    $imageInfo = getimagesize($sourcePath);
    if (!$imageInfo) {
        return false;
    }
    
    $originalWidth = $imageInfo[0];
    $originalHeight = $imageInfo[1];
    $mimeType = $imageInfo['mime'];
    
    // Bereken nieuwe afmetingen (behoud aspect ratio)
    $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
    $newWidth = intval($originalWidth * $ratio);
    $newHeight = intval($originalHeight * $ratio);
    
    // Maak source image resource
    switch ($mimeType) {
        case 'image/jpeg':
            $sourceImage = imagecreatefromjpeg($sourcePath);
            break;
        case 'image/png':
            $sourceImage = imagecreatefrompng($sourcePath);
            break;
        case 'image/gif':
            $sourceImage = imagecreatefromgif($sourcePath);
            break;
        case 'image/webp':
            $sourceImage = imagecreatefromwebp($sourcePath);
            break;
        default:
            return false;
    }
    
    if (!$sourceImage) {
        return false;
    }
    
    // Maak thumbnail canvas
    $thumbnail = imagecreatetruecolor($newWidth, $newHeight);
    
    // Behoud transparantie voor PNG en GIF
    if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
        imagealphablending($thumbnail, false);
        imagesavealpha($thumbnail, true);
        $transparent = imagecolorallocatealpha($thumbnail, 255, 255, 255, 127);
        imagefilledrectangle($thumbnail, 0, 0, $newWidth, $newHeight, $transparent);
    }
    
    // Resize de afbeelding
    imagecopyresampled(
        $thumbnail, $sourceImage,
        0, 0, 0, 0,
        $newWidth, $newHeight,
        $originalWidth, $originalHeight
    );
    
    // Sla thumbnail op
    $success = false;
    switch ($mimeType) {
        case 'image/jpeg':
            $success = imagejpeg($thumbnail, $destPath, 90);
            break;
        case 'image/png':
            $success = imagepng($thumbnail, $destPath, 8);
            break;
        case 'image/gif':
            $success = imagegif($thumbnail, $destPath);
            break;
        case 'image/webp':
            $success = imagewebp($thumbnail, $destPath, 90);
            break;
    }
    
    // Clean up
    imagedestroy($sourceImage);
    imagedestroy($thumbnail);
    
    return $success;
}

    /**
 * Upload en resize een avatar automatisch
 *
 * @param array $file $_FILES element
 * @param int $userId Gebruiker ID voor bestandsnaam
 * @param int $size Gewenste grootte (vierkant)
 * @return array Upload resultaat met thumbnail info
 */
function upload_avatar($file, $userId, $size = 400) {
    // Basis upload met strikte avatar validatie
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $maxSize = 2 * 1024 * 1024; // 2MB
    
    $uploadResult = upload_file(
        $file,
        'avatars',
        $allowedTypes,
        $maxSize,
        'avatar_' . $userId . '_'
    );
    
    if (!$uploadResult['success']) {
        return $uploadResult;
    }
    
    // Maak een vierkante thumbnail voor de avatar
    $originalPath = $uploadResult['full_path'];
    $thumbnailPath = str_replace('.', '_thumb.', $originalPath);
    
    if (create_thumbnail($originalPath, $thumbnailPath, $size, $size)) {
        // Vervang origineel door thumbnail voor avatars (scheelt ruimte)
        if (unlink($originalPath) && rename($thumbnailPath, $originalPath)) {
            $uploadResult['resized'] = true;
            $uploadResult['size'] = $size . 'x' . $size;
        }
    }
    
    return $uploadResult;
}

    /**
 * Valideer en optimaliseer afbeelding voor web gebruik
 *
 * @param string $filePath Pad naar afbeelding
 * @param int $maxWidth Maximale breedte
 * @param int $maxHeight Maximale hoogte
 * @param int $quality JPEG kwaliteit (1-100)
 * @return bool True als succesvol geoptimaliseerd
 */
function optimize_image($filePath, $maxWidth = 1200, $maxHeight = 1200, $quality = 85) {
    if (!file_exists($filePath)) {
        return false;
    }
    
    $imageInfo = getimagesize($filePath);
    if (!$imageInfo) {
        return false;
    }
    
    $originalWidth = $imageInfo[0];
    $originalHeight = $imageInfo[1];
    $mimeType = $imageInfo['mime'];
    
    // Check of resize nodig is
    if ($originalWidth <= $maxWidth && $originalHeight <= $maxHeight) {
        return true; // Al optimaal
    }
    
    // Bereken nieuwe afmetingen
    $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
    $newWidth = intval($originalWidth * $ratio);
    $newHeight = intval($originalHeight * $ratio);
    
    // Maak geoptimaliseerde versie
    $tempPath = $filePath . '.temp';
    if (create_thumbnail($filePath, $tempPath, $newWidth, $newHeight)) {
        // Vervang origineel door geoptimaliseerde versie
        if (unlink($filePath) && rename($tempPath, $filePath)) {
            return true;
        }
    }
    
    return false;
}

    /**
 * Haal afbeelding metadata op
 *
 * @param string $filePath Pad naar afbeelding
 * @return array|false Metadata array of false bij fout
 */
function get_image_metadata($filePath) {
    if (!file_exists($filePath)) {
        return false;
    }
    
    $imageInfo = getimagesize($filePath);
    if (!$imageInfo) {
        return false;
    }
    
    return [
        'width' => $imageInfo[0],
        'height' => $imageInfo[1],
        'type' => $imageInfo[2],
        'mime' => $imageInfo['mime'],
        'size' => filesize($filePath),
        'size_formatted' => format_filesize(filesize($filePath)),
        'aspect_ratio' => round($imageInfo[0] / $imageInfo[1], 2)
    ];
}

    /**
     * Verwijdert een geüpload bestand
     *
     * @param string $relativePath Relatief pad naar het bestand
     * @return bool True als succesvol verwijderd, anders false
     */
    function delete_uploaded_file($relativePath) {
        $fullPath = BASE_PATH . '/public/uploads/' . $relativePath;
        
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        
        return false;
    }

    /**
     * Geeft een leesbaar error-bericht voor upload fouten
     *
     * @param int $errorCode Upload error code
     * @return string Error message
     */
    function upload_error_message($errorCode) {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
                return 'Het bestand is groter dan de in php.ini toegestane grootte';
            case UPLOAD_ERR_FORM_SIZE:
                return 'Het bestand is groter dan de in het formulier toegestane grootte';
            case UPLOAD_ERR_PARTIAL:
                return 'Het bestand is slechts gedeeltelijk geüpload';
            case UPLOAD_ERR_NO_FILE:
                return 'Er is geen bestand geüpload';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Er is geen tijdelijke map gevonden';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Kan het bestand niet naar de schijf schrijven';
            case UPLOAD_ERR_EXTENSION:
                return 'Bestandsupload gestopt door een PHP-extensie';
            default:
                return 'Onbekende upload fout';
        }
    }

    /**
     * Formatteert bestandsgrootte naar leesbaar formaat
     *
     * @param int $bytes Bestandsgrootte in bytes
     * @return string Geformatteerde bestandsgrootte
     */
    function format_filesize($bytes) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Geeft de URL voor een geüpload bestand
     *
     * @param string $relativePath Relatief pad naar het bestand
     * @return string URL naar het bestand
     */
    function uploaded_file_url($relativePath) {
        return base_url('public/uploads/' . $relativePath);
    }