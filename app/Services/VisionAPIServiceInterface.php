<?php

namespace App\Services;

interface VisionAPIServiceInterface
{
    public function analyzeImage(string $imagePath): array;
}
