<?php

namespace App\Command;

use App\Entity\AccountType;
use App\Repository\AccountTypeRepository;
use App\Service\BudgetInsightApiService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:load-account-types')]
class LoadAccountTypesCommand extends Command
{
    /** @var AccountType[] */
    private array $accountTypes;

    public function __construct(
        private readonly BudgetInsightApiService $api,
        private readonly EntityManagerInterface  $em,
        private readonly LoggerInterface         $logger,
        private readonly AccountTypeRepository   $accountTypeRepo,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $accountTypesFromDb = $this->accountTypeRepo->findAllIds();

        $this->accountTypes = $this->api->listAccountTypes();

        $numberOfAccountTypes = count($this->accountTypes);

        $this->logger->info("$numberOfAccountTypes account types will be loaded");

        foreach ($this->accountTypes as $accountType) {
            if (!isset($accountTypesFromDb[$accountType->getId()])) {
                $parent = $this->findAccountTypeById($accountType->getIdParent());
                $accountType->setParent($parent);
                $this->em->persist($accountType);
            }
        }

        $this->em->flush();

        $this->logger->info("All account types are successfully loaded in database");

        return Command::SUCCESS;
    }

    private function findAccountTypeById(?int $id): ?AccountType
    {
        if (!$id) {
            return null;
        }

        foreach ($this->accountTypes as $accountType) {
            if ($id === $accountType->getId()) {
                return $accountType;
            }
        }
    }
}