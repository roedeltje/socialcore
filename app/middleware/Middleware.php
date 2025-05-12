<?php
namespace App\Middleware;

interface Middleware
{
    /**
     * Voer de middleware uit
     * 
     * @return bool True als de request verder mag, false om te stoppen
     */
    public function handle(): bool;
}