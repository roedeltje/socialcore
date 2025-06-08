<?php

namespace App\Helpers;

class Logger
{
    private static $logPath = null;
    
    /**
     * Initialize the logger with the log directory
     */
    public static function init($logDirectory = null)
    {
        if ($logDirectory === null) {
            $logDirectory = BASE_PATH . '/logs';
        }
        
        // Maak log directory als het niet bestaat
        if (!is_dir($logDirectory)) {
            mkdir($logDirectory, 0755, true);
        }
        
        self::$logPath = $logDirectory;
    }
    
    /**
     * Log een debug bericht
     */
    public static function debug($message, $context = [])
    {
        self::writeLog('DEBUG', $message, $context);
    }
    
    /**
     * Log een info bericht
     */
    public static function info($message, $context = [])
    {
        self::writeLog('INFO', $message, $context);
    }
    
    /**
     * Log een warning
     */
    public static function warning($message, $context = [])
    {
        self::writeLog('WARNING', $message, $context);
    }
    
    /**
     * Log een error
     */
    public static function error($message, $context = [])
    {
        self::writeLog('ERROR', $message, $context);
    }
    
    /**
     * Speciale functie voor photo upload debugging
     */
    public static function photoUpload($step, $data = [])
    {
        $message = "PHOTO UPLOAD - $step";
        self::writeLog('PHOTO', $message, $data);
    }
    
    /**
     * Schrijf naar het log bestand
     */
    private static function writeLog($level, $message, $context = [])
    {
        if (self::$logPath === null) {
            self::init();
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $logFile = self::$logPath . '/socialcore-' . date('Y-m-d') . '.log';
        
        // Format de log entry
        $logEntry = "[{$timestamp}] {$level}: {$message}";
        
        // Voeg context toe als er data is
        if (!empty($context)) {
            $logEntry .= "\nContext: " . json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
        
        $logEntry .= "\n" . str_repeat('-', 80) . "\n";
        
        // Schrijf naar bestand
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
        
        // Ook naar PHP error log sturen (optioneel)
        error_log("SocialCore {$level}: {$message}");
    }
    
    /**
     * Haal de laatste log entries op
     */
    public static function getRecentLogs($lines = 50)
    {
        if (self::$logPath === null) {
            self::init();
        }
        
        $logFile = self::$logPath . '/socialcore-' . date('Y-m-d') . '.log';
        
        if (!file_exists($logFile)) {
            return "Geen log bestand gevonden voor vandaag.";
        }
        
        $command = "tail -n {$lines} " . escapeshellarg($logFile);
        return shell_exec($command);
    }
    
    /**
     * Maak log bestanden leeg (voor opruiming)
     */
    public static function clearLogs()
    {
        if (self::$logPath === null) {
            self::init();
        }
        
        $logFiles = glob(self::$logPath . '/socialcore-*.log');
        foreach ($logFiles as $file) {
            unlink($file);
        }
    }
}