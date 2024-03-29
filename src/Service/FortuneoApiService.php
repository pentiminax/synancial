<?php

namespace App\Service;

use App\Model\News;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FortuneoApiService
{
    private array $options = [];

    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly SerializerInterface $serializer
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

        if (!isset($data['news'])) {
            return [];
        }


        return $this->serializer->deserialize(json_encode($data['news']), 'App\Model\News[]', 'json');

    }

    private function request(string $method, string $url): string|array
    {
        $response = $this->client->request($method, $url, $this->options);


        return $response->getContent(false);
    }
}