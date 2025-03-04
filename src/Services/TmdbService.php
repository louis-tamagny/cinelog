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
                'query' => $query,
                'language' => 'fr-FR'
            ]
        ]);

        $data = $response->toArray();

        return array_slice($data['results'], 0, $limit);
    }

    // Récupérer les information d'un film par son ID
    public function getMovieDetails(int $movieId): array
    {
        $response = $this->client->request('GET', "{$this->baseUrl}/movie/{$movieId}", [
            'query' => ['api_key' => $this->apiKey, 'language' => 'fr-FR']
        ]);
        return $response->toArray();
    }



    public function getMovieTrailer(int $movieId): ?string
    {
        // Send request to TMDB API to get movie videos (trailer)
        $response = $this->client->request('GET', "{$this->baseUrl}/movie/{$movieId}/videos", [
            'query' => [
                'api_key' => $this->apiKey,
                'language' => 'fr-FR',
            ]
        ]);
    
        // Check the response and return the trailer key
        $data = $response->toArray();
    
        // Loop through the results to find a video with type 'Trailer'
        foreach ($data['results'] as $video) {
            if ($video['type'] === 'Trailer') {
                return $video['key'];  // Return the YouTube key for the trailer
            }
        }
    
        return null;  // No trailer found
    }
    
}
