<?php

namespace App\Command;

use App\Repository\ConnectorRepository;
use App\Service\BudgetInsightApiService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LoadConnectorsCommand extends Command
{
    protected  static $defaultName = 'app:load-connectors';

    public function __construct(
        private readonly BudgetInsightApiService $api,
        private readonly EntityManagerInterface  $em,
        private readonly LoggerInterface $logger,
        private readonly ConnectorRepository $connectorRepo
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $connectorsFromDb = $this->connectorRepo->findAllIds();

        $connectors = $this->api->listConnectors();

        $numberOfConnectors = count($connectors);

        $this->logger->info("$numberOfConnectors connectors will be loaded");

        foreach ($connectors as $connector) {
            if (!isset($connectorsFromDb[$connector->getId()])) {
                $this->em->persist($connector);
            }
        }

        $this->em->flush();

        $this->logger->info("All connectors are successfully loaded in database");

        return Command::SUCCESS;
    }
}