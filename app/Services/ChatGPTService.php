<?php


namespace App\Services;

use GuzzleHttp\Client;

class ChatGPTService implements ChatGPTServiceInterface
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env('CHATGPT_API_KEY'); // Make sure to set this in your .env file
    }

    public function generateResponse(string $prompt): string
    {
        $endpoint = 'https://api.openai.com/v1/chat/completions';

        // Prepare the request data
        $postData = [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a helpful assistant.',
                ],
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
            ],
            'max_tokens' => 150, // Limit the length of the response
            'n' => 1,
            'stop' => null,
            'temperature' => 0.7, // Adjust creativity level
        ];

        // Set up the request headers
        $headers = [
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ];

        try {
            // Send the request to ChatGPT
            $response = $this->client->post($endpoint, [
                'headers' => $headers,
                'json' => $postData,
            ]);

            $body = json_decode($response->getBody(), true);
            return trim($body['choices'][0]['message']['content'] ?? 'No response generated.');
        } catch (\Exception $e) {
            // Log the error and return a default message
            \Log::error('ChatGPT API Error: ' . $e->getMessage());
            return 'Unable to generate a response.';
        }
    }
}
