<?php

namespace App\Command;

use App\Entity\Symbol;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(name: 'app:load-symbols')]
class LoadSymbolsCommand extends Command
{
    const BATCH_SIZE = 100;

    const LIMIT = 100;

    const SYMBOLS_ENDPOINT = '/symbols';

    public function __construct(
        private readonly EntityManagerInterface  $em,
        private readonly HttpClientInterface $divvydiaryClient,
        private readonly LoggerInterface $logger,
        private readonly SerializerInterface $serializer
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $total = json_decode($this->divvydiaryClient->request(Request::METHOD_GET, self::SYMBOLS_ENDPOINT)->getContent(), true)['total'];

        $this->logger->info("Number of symbols: $total");

        $numberOfPages = round($total / self::LIMIT);

        $this->logger->info("Number of pages: $numberOfPages");

        for($page = 0; $page <= $numberOfPages; $page++) {
            $query = http_build_query([
                'limit' => self::LIMIT,
                'page' => $page,
            ]);

            $data = json_decode($this->divvydiaryClient->request(Request::METHOD_GET, self::SYMBOLS_ENDPOINT . "?$query")->getContent(), true);

            /** @var Symbol[] $symbols */
            $symbols = $this->serializer->deserialize(json_encode($data['symbols']), 'App\Entity\Symbol[]', 'json');

            foreach ($symbols as $symbol) {
                $this->logger->info("Persisting symbol with ISIN: {$symbol->getIsin()}");
                $this->em->persist($symbol);
            }

            $this->em->flush();
            $this->em->clear();
        }

        return Command::SUCCESS;
    }
}