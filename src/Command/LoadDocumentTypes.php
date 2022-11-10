<?php

namespace App\Command;

use App\Repository\ConnectorRepository;
use App\Service\BudgetInsightApiService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:load-document-types')]
class LoadDocumentTypes extends Command
{
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
        $documentsFromDb = $this->connectorRepo->findAllIds();

        $documentTypes = $this->api->listConnectors();

        $numberOfDocumentTypes = count($documentTypes);

        $this->logger->info("$numberOfDocumentTypes connectors will be loaded");

        foreach ($documentTypes as $documentType) {
            if (!isset($documentsFromDb[$documentType->getId()])) {
                $this->em->persist($documentType);
            }
        }

        $this->em->flush();

        $this->logger->info("All connectors are successfully loaded in database");

        return Command::SUCCESS;
    }
}