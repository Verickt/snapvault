<?php

namespace App\Services;

use GuzzleHttp\Client;

interface ChatGPTServiceInterface
{

    public function generateResponse(string $prompt): string;
}
