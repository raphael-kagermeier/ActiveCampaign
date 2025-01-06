<?php

namespace PerformRomance\ActiveCampaign\Support;

use GuzzleHttp\Client;
use PerformRomance\ActiveCampaign\Exceptions\ActiveCampaignException;

class Request
{
    private readonly Client $client;

    public function __construct(
        private readonly string $api_url,
        private readonly string $api_key,
        private readonly string $api_version,
        ?Client $client = null
    ) {
        $this->client = $client ?? new Client();
    }

    /**
     * Make an HTTP request to ActiveCampaign API
     *
     * @throws ActiveCampaignException
     */
    public function make(string $endpoint, array $data = [], string $method = 'POST', array $query = []): object
    {
        $options = [
            'headers' => [
                'Api-Token' => $this->api_key,
                'Content-Type' => 'application/json',
            ],
            'verify' => false,
        ];

        if (!empty($query)) {
            $options['query'] = $query;
        }

        if ($method !== 'GET' && !empty($data)) {
            $options['json'] = $data;
        }

        try {
            $response = $this->client->request($method, $endpoint, $options);

            if (!in_array($response->getStatusCode(), [200, 201])) {
                throw new ActiveCampaignException(
                    "Request failed with status code: {$response->getStatusCode()}"
                );
            }

            return json_decode($response->getBody()->getContents());
        } catch (\Throwable $e) {
            throw new ActiveCampaignException(
                "ActiveCampaign API request failed: {$e->getMessage()}",
                $e->getCode(),
                $e
            );
        }
    }

    public function getEndpoint(string $path): string
    {
        return "{$this->api_url}/api/{$this->api_version}/{$path}";
    }
}
