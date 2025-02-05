<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class TmdbService
{
    private string $apiKey;
    private string $baseUrl;
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client, string $apiKey, string $baseUrl)
    {
        $this->client = $client;
        $this->apiKey = $apiKey;
        $this->baseUrl = $baseUrl;
    }

    // Récupérer la liste des 5 premiers films par rapport à une recherche
    public function searchMovies(string $query, int $limit = 5): array
    {
        $response = $this->client->request('GET', "{$this->baseUrl}/search/movie", [
            'query' => [
                'api_key' => $this->apiKey,
                'query' => $query
            ]
        ]);

        $data = $response->toArray();

        return array_slice($data['results'], 0, $limit);
    }

    // Récupérer les information d'un film par son ID
    public function getMovieDetails(int $movieId): array
    {
        $response = $this->client->request('GET', "{$this->baseUrl}/movie/{$movieId}", [
            'query' => ['api_key' => $this->apiKey]
        ]);
    
        return $response->toArray();
    }

}
