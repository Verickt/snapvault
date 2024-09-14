<?php

namespace App\Services;

use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\Feature\Type;
use Google\Cloud\Vision\V1\Likelihood;
use Google\Cloud\Vision\V1\Feature;
use Google\Cloud\Vision\V1\Image;
use Google\Cloud\Vision\V1\AnnotateImageRequest;

class VisionAPIService implements VisionAPIServiceInterface
{

    protected $client;

    public function __construct()
    {
        // Ensure the GOOGLE_APPLICATION_CREDENTIALS environment variable is set
        $this->client = new ImageAnnotatorClient();
    }

    public function analyzeImage(string $imagePath): array
    {
        if (!file_exists($imagePath)) {
            throw new \Exception("Image file not found: " . $imagePath);
        }

        // Read the image content
        $imageContent = file_get_contents($imagePath);

        // Create an Image object and set its content
        $image = new Image();
        $image->setContent($imageContent);

        // Define the features you want to extract
        $features = [
            new Feature(['type' => Type::LABEL_DETECTION]),
            new Feature(['type' => Type::TEXT_DETECTION]),
            new Feature(['type' => Type::IMAGE_PROPERTIES]),
            new Feature(['type' => Type::SAFE_SEARCH_DETECTION]),
        ];

        // Create the AnnotateImageRequest object
        $requests = [
            new AnnotateImageRequest([
                'image' => $image,
                'features' => $features,
            ]),
        ];

        // Call the batchAnnotateImages method
        $response = $this->client->batchAnnotateImages($requests);

        // Extract the first response
        $annotation = $response->getResponses()[0];

        // Check for errors in the response
        if ($annotation->hasError()) {
            $error = $annotation->getError();
            throw new \Exception('API Error: ' . $error->getMessage());
        }

        // Initialize the result array
        $result = [
            'title' => '',
            'description' => '',
            'categories' => [],
            'colors' => [],
            'is_safe' => true,
        ];

        // Extract labels (categories)
        $labels = $annotation->getLabelAnnotations();
        if ($labels) {
            foreach ($labels as $label) {
                $result['categories'][] = $label->getDescription();
            }
        }


    // Extract text
        $texts = $annotation->getTextAnnotations();
        if ($texts && count($texts) > 0) {
            $firstText = $texts[0];
            $result['title'] = $firstText->getDescription();
            $result['description'] = substr($firstText->getDescription(), 0, 1000);
        }

        // Extract dominant colors
        $properties = $annotation->getImagePropertiesAnnotation();
        if ($properties && $properties->getDominantColors()) {
            $colors = $properties->getDominantColors()->getColors();
            $result['colors'] = []; // Ensure the 'colors' key is initialized
            $counter = 0;
            $maxColors = 5; // Limit to 5 colors
            foreach ($colors as $color) {
                if ($counter >= $maxColors) {
                    break;
                }
                $result['colors'][] = sprintf(
                    '#%02x%02x%02x',
                    $color->getColor()->getRed(),
                    $color->getColor()->getGreen(),
                    $color->getColor()->getBlue()
                );
                $counter++;
            }
        }


        // Check if the image is safe
        $safeSearch = $annotation->getSafeSearchAnnotation();
        if ($safeSearch) {
            $result['is_safe'] =
                $safeSearch->getAdult() !== Likelihood::LIKELY &&
                $safeSearch->getAdult() !== Likelihood::VERY_LIKELY &&
                $safeSearch->getViolence() !== Likelihood::LIKELY &&
                $safeSearch->getViolence() !== Likelihood::VERY_LIKELY;
        }

        return $result;
    }
}
