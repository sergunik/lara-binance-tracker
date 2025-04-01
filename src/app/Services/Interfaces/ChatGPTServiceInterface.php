<?php

namespace App\Services\Interfaces;

interface ChatGPTServiceInterface
{
    public function sendMessage(string $message): string;
} 