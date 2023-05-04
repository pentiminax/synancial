<?php

namespace App\Command;

use App\Entity\Dividend;
use App\Repository\SymbolRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(name: 'app:load-dividends')]
class LoadDividendsCommand extends Command
{
    const BATCH_SIZE = 50;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly HttpClientInterface $divvydiaryClient,
        private readonly SerializerInterface $serializer,
        private readonly SymbolRepository $symbolRepo
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $symbols = $this->symbolRepo->findAllISINThatPayDividends();

        for ($i = 0; $i < count($symbols); $i++) {
            $symbol = $symbols[$i];

            $data = json_decode($this->divvydiaryClient->request(Request::METHOD_GET, "/symbols/{$symbol->getIsin()}")->getContent(), true);

            /** @var Dividend[] $dividends */
            $dividends = $this->serializer->deserialize(json_encode($data['dividends']), 'App\Entity\Dividend[]', 'json');

            foreach ($dividends as $dividend) {
                $dividend->setSymbol($symbol);
                $this->em->persist($dividend);
            }

            $this->em->flush();
        }

        return Command::SUCCESS;
    }
}