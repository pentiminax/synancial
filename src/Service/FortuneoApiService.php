<?php

namespace App\Service;

use App\Model\News;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FortuneoApiService
{
    const NEWS_ENDPOINT = '/news';

    private array $options = [];

    public function __construct(
        private HttpClientInterface $client,
        private SerializerInterface $serializer
    )
    {
    }

    /**
     * @return News[]
     */
    public function listNews(): array
    {
        $baseUrl = 'https://bourse.fortuneo.fr/api';

        $this->options['query'] = [
            'newsSource' => '-1',
            'newsType' => 'actualites',
            'page' => 1
        ];

        $data = json_decode($this->request(Request::METHOD_GET, "$baseUrl/news/"), true);

        return $this->serializer->deserialize(json_encode($data['news']), 'App\Model\News[]', 'json');

    }

    private function request(string $method, string $url): string|array
    {
        $response = $this->client->request($method, $url, $this->options);

        return $response->getContent();
    }
}