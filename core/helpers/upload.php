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
    $newFilename = generate_unique_filename($file['name'], $prefix);
    
    // Bepaal upload pad
    $uploadDir = get_upload_path($type);
    $uploadPath = $uploadDir . '/' . $newFilename;
    
    // Upload het bestand
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        // Bereken het relatieve pad voor gebruik in de database
        $year = date('Y');
        $month = date('m');
        $relativePath = $type . '/' . $year . '/' . $month . '/' . $newFilename;
        
        return [
            'success' => true,
            'message' => 'Bestand succesvol geüpload',
            'path' => $relativePath,
            'filename' => $newFilename,
            'full_path' => $uploadPath
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Fout bij het opslaan van het bestand',
            'path' => null
        ];
    }
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