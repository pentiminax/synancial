<?php

namespace App\Controller;

use App\Entity\AccountType;
use App\Entity\Connection;
use App\Entity\User;
use App\Model\ApiResponse;
use App\Model\Dashboard\DashboardData;
use App\Model\Wallet\Checking\CheckingData;
use App\Model\Wallet\Loans\LoansData;
use App\Model\Wallet\Savings\SavingsData;
use App\Model\Wallet\WalletData;
use App\Repository\ConnectorRepository;
use App\Service\ApiService;
use App\Service\BudgetInsightApiService;
use App\Service\ConnectionService;
use App\Service\DividendService;
use App\Service\TimeSerieService;
use App\Service\UserService;
use App\Service\UserSessionService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @method User getUser()
 */
#[Route('/api')]
class ApiController extends AbstractController
{
    public function __construct(
        private readonly BudgetInsightApiService $api,
        private readonly ConnectorRepository     $connectorRepo,
        private readonly UserSessionService      $userSessionService,
        private readonly EntityManagerInterface  $em
    )
    {
    }

    #[Route('/connections/{id}', name: 'api_delete_connections', methods: ['DELETE'])]
    public function deleteConnection(int $id): Response
    {
        $status = $this->api->deleteConnection($id);

        return $this->json(new ApiResponse(), $status);
    }

    #[Route('/users/me', name: 'api_users_me', methods: ['GET'])]
    public function user(): Response
    {
        $user = $this->getUser();

        return $this->json([
            'secretMode' => $user->isIsSecretModeEnabled()
        ]);
    }

    #[Route('/users/me', name: 'api_users_me_update', methods: ['PUT'])]
    public function updateUser(Request $request, UserService $userService): Response
    {
        $userService->updateUser($request->getContent());

        return $this->json(new ApiResponse());
    }

    #[Route('/users/me/accounts/{id}/transactions', name: 'api_users_me_accounts_transaction', methods: ['GET'])]
    public function getTransactionsByAccount(int $id, Request $request): Response
    {
        $offset = $request->query->getInt('offset');
        $limit = $request->query->getInt('limit', 10);

        $transactions = $this->api->listTransactions($id, $offset, $limit);

        return $this->json([
            'error' => 'null',
            'message' => 'OK',
            'result' => $this->renderView('partials/_transactions_list.html.twig', [
                'transactions' => $transactions
            ])
        ]);
    }

    #[Route('/users/me/views/dashboard', name: 'api_users_me_views_dashboard')]
    public function dashboard(ApiService $apiService): Response
    {
        if (!$this->getUser()->getBearerToken()) {
            return $this->redirectToRoute('security_logout');
        }

        $dashboardData = $this->userSessionService->getDashboardData();

        if (!$dashboardData) {
            $dashboardData = new DashboardData();
            $apiService->aggregateAssetsAccounts($dashboardData, $this->api->listBankAccounts());
            $apiService->aggregateCrowdlendingsAccounts($dashboardData, $this->getUser());
            $dashboardData->processCalculatedData();
            $this->userSessionService->setDashboardData($dashboardData);
        }

        return $this->json(new ApiResponse(result: $dashboardData));
    }

    #[Route('/users/me/views/wallet', name: 'api_users_me_views_wallet')]
    public function wallet(ApiService $apiService): Response
    {
        $walletData = $this->userSessionService->getWalletData();

        if (!$walletData) {
            $walletData = new WalletData();
            $bankAccounts = $this->api->listBankAccounts();
            $apiService->aggregateAssetsAccounts($walletData, $bankAccounts);
            $walletData->processCalculatedData();
            $this->userSessionService->setWalletData($walletData);
        }

        $walletData = [
            'assets' => $this->renderView('wallet/_assets_table.html.twig', [
                'assets' => $walletData->getDistribution()->getAssets()
            ]),
            'liabilities' => $this->renderView('wallet/_liabilities_table.html.twig', [
                'liabilities' => $walletData->getDistribution()->getLiabilities()
            ])
        ];

        return $this->json(new ApiResponse(result: $walletData));
    }

    #[Route('/users/me/views/wallet/checking', name: 'api_users_me_views_wallet_checking', methods: ['GET'])]
    public function accounts(): Response
    {
        $checkingData = $this->userSessionService->getCheckingData();

        if (!$checkingData) {
            $checkingData = new CheckingData();
            $data = [];
            $bankAccounts = $this->api->listBankAccounts([AccountType::CHECKING]);

            foreach ($bankAccounts as $bankAccount) {
                $connection = $this->getUser()->findConnection($bankAccount->id_connection);

                if (!$connection) {
                    continue;
                }

                $connector = $connection->getConnector();

                if (!isset($data[$connector->getSlug()])) {
                    $data[$connector->getSlug()] = [
                        'name' => $connector->getName()
                    ];
                }

                $data[$connector->getSlug()]['accounts'][] = $bankAccount;
            }

            $checkingData->setData($data);
            $this->userSessionService->setCheckingData($checkingData);
        }

        $accountsList = $this->renderView('wallet/checking/_card_list.html.twig', [
            'banks' => $checkingData->getData()
        ]);

        return $this->json(new ApiResponse(result: $accountsList));
    }

    #[Route('/users/me/views/wallet/market', name: 'api_wallet_market_list')]
    #[Cache(public: true, maxage: 3600, mustRevalidate: true)]
    public function marketList(): Response
    {
        $accounts = $this->api->listBankAccounts([AccountType::LIFEINSURANCE, AccountType::MARKET, AccountType::PEA]);

        $result['investments'] = '';

        foreach ($accounts as $account) {
            $result['investments'] .= $this->renderView('wallet/market/_investments_accordion.html.twig', [
                'account' => $account,
                'investments' =>  $this->api->listInvestmentsByAccount($account->id)
            ]);
        }

        return $this->json(new ApiResponse(result: $result));
    }

    #[Route('/users/me/views/wallet/market/{id}', name: 'api_wallet_market_view')]
    public function marketView(int $id, DividendService $dividendService): Response
    {
        $account = $this->api->getBankAccount($id);

        $result['investments'] = '';
        $result['distribution'] = [
            'datasets' => [
                'data' => []
            ],
            'labels' => []
        ];

        $totalValue = 0;
        $totalAnnualDividend = 0;

        $investments = $this->api->listInvestmentsByAccount($account->id);

        $result['investments'] .= $this->renderView('wallet/market/_investments_accordion.html.twig', [
            'account' => $account,
            'investments' => $investments,
            'isView' => true
        ]);

        if (AccountType::MARKET === $account->type || AccountType::PEA === $account->type) {
            $totalAnnualDividend += $dividendService->getDividendsAmountByInvestments($investments);

            foreach ($investments as $investment) {
                $totalValue += $investment->unitvalue * $investment->quantity;
            }

            foreach ($investments as $investment) {
                $result['distribution']['labels'][] = $investment->label;
                $result['distribution']['datasets']['data'][] = round((($investment->unitvalue * $investment->quantity) / $totalValue) * 100);
            }
        }

        $result['totalAnnualDividend'] = $totalAnnualDividend;
        $result['totalValue'] = round($totalValue);
        $result['numberOfAssets'] = count($result['distribution']['datasets']['data']);

        return $this->json(new ApiResponse(result: $result));
    }

    #[Route('/users/me/views/wallet/loans', name: 'api_users_me_views_wallet_loans')]
    public function loansList(): Response
    {
        $loansData = $this->userSessionService->getLoansData();

        if (!$loansData) {
            $data = [];
            $loanAccounts = $this->api->listBankAccounts([AccountType::LOAN]);
            $loansData = new LoansData();

            $data['monthly'] = [
                'repayment' => 0,
                'capital' => 0
            ];

            foreach ($loanAccounts as $account) {
                $loan = $account->loan;

                $data['loans'][$account->id] = [
                    'id' => $account->id,
                    'name' => $account->name,
                    'total_amount' => $loan['total_amount'],
                    'balance' => abs($account->balance),
                    'total_repayment' => $loan['total_amount'] - abs($account->balance),
                    'repayment' => $loan['next_payment_amount']
                ];

                $data['monthly']['repayment'] += $loan['next_payment_amount'];
                $data['monthly']['capital'] += $loan['next_payment_amount'];
            }

            $loansData->setData($data);
        }

        $loansList = $this->renderView('wallet/loans/_card_list.html.twig', [
            'data' => $loansData->getData()
        ]);


        $result = [];
        return $this->json(new ApiResponse(result: $loansList));
    }

    #[Route('/users/me/views/wallet/savings', name: 'api_users_me_views_wallet_savings')]
    public function savingsView(): Response
    {
        $savingsData = $this->userSessionService->getSavingsData();

        if (!$savingsData) {
            $savingsData = new SavingsData();
            $data = [];
            $savingsAccount = $this->api->listBankAccounts([AccountType::SAVINGS]);

            foreach ($savingsAccount as $savingAccount) {
                $connection = $this->getUser()->findConnection($savingAccount->id_connection);

                if (!$connection) {
                    continue;
                }

                $connector = $connection->getConnector();

                if (!isset($data[$connector->getSlug()])) {
                    $data[$connector->getSlug()] = [
                        'name' => $connector->getName()
                    ];
                }

                $data[$connector->getSlug()]['accounts'][] = $savingAccount;
            }

            $savingsData->setData($data);
        }

        $accountsList = $this->renderView('wallet/checking/_card_list.html.twig', [
            'banks' => $savingsData->getData()
        ]);

        return $this->json(new ApiResponse(result: $accountsList));
    }

    #[Route('/users/me/connections', name: 'api_users_me_connections')]
    public function connections(): Response
    {
        $accounts = [];
        $connectorsData = [];

        $connections = $this->api->listConnections();
        $connectorIds = [];

        foreach ($connections as $connection) {
            $connectorIds[] = $connection->id_connector;
        }

        $connectors = $this->connectorRepo->findBy([
            'id' => $connectorIds
        ]);

        foreach ($connectors as $connector) {
            $connectorsData[$connector->getId()] = [
                'name' => $connector->getName(),
                'slug' => $connector->getSlug()
            ];
        }

        foreach ($connections as $connection) {
            $data = $connectorsData[$connection->id_connector];

            $accounts[] = [
                'connection_id' => $connection->id,
                'id' => $connection->connector_uuid,
                'name' => $data['name'],
                'slug' => $data['slug'],
                'state' => $connection->state,
                'last_sync_at' => $connection->last_update
            ];
        }

        $result = $this->renderView('user/_linked_accounts.html.twig', [
            'accounts' => $accounts
        ]);

        return $this->json(new ApiResponse(null, 'OK', $result));
    }

    #[Route('/users/me/manage/connection', name: 'api_users_me_manage_connection')]
    public function manageConnection(Request $request): Response
    {
        $bearerToken = $this->getUser()->getBearerToken();

        $url = $this->api->getWebviewUrl($bearerToken, 'manage');

        $name = $request->query->get('name');

        $this->addFlash('success', "$name est connecté, nous récupérons les données.");

        return $this->redirect($url);
    }

    #[Route('/users/me/sync', name: 'api_users_me_sync', methods: ['PUT'])]
    public function sync(LoggerInterface $logger): Response
    {
        $user = $this->getUser();
        $now = new \DateTime();

        $connectionIds = $user->getConnections()
            ->filter(fn(Connection $connection) => $connection->getLastUpdate()->diff($now)->h > 1 || $connection->getLastUpdate()->diff($now)->d > 1)
            ->map(fn(Connection $connection) => $connection->getId())
            ->toArray();

        foreach ($connectionIds as $connectionId) {
            $connection = $this->api->updateConnection($connectionId);
            $logger->info('Synchronization result:' . json_encode($connection));
        }

        $user->setLastSync($now);

        $this->em->flush();

        return $this->json([
            'error' => null,
            'message' => 'OK',
            'result' => []
        ]);
    }

    #[Route('/users/me/timeseries/{id}', name: 'api_users_me_timeseries')]
    public function timeseries(?int $id, TimeSerieService $timeSerieService): Response
    {
        $line = [];
        $bar = [];
        $max = 0;

        $timeSerieService->processLineChart($id, $line);
        $timeSerieService->processBarChart($line, $bar, $max);

        return $this->json([
            'error' => null,
            'message' => 'OK',
            'result' => [
                'line' => $line,
                'bar' => $bar,
                'max' => $max,
                'min' => -1 * abs($max)
            ]
        ]);
    }

    #[Route('/users/me/webview', name: 'api_users_me_webview')]
    public function webview(Request $request, ConnectionService $connectionService, UserSessionService $userSessionService): Response
    {
        $error = $request->get('error');

        if ($error) {
            return $this->redirectToRoute('dashboard');
        }

        $code = $request->get('code');
        $connectorUuids = $request->get('connector_uuids');
        $connectionId = $request->get('connection_id');

        if ($code) {
            $permanentUserAccessToken = $this->api->generatePermanentUserAccessToken($code);
            $this->getUser()->setBearerToken($permanentUserAccessToken->access_token);
            $this->em->flush();
        }

        if ($connectorUuids && $connectionId) {
            $connector = $this->connectorRepo->findOneBy(['uuid' => $connectorUuids]);

            if ($connector) {
                $connectionService->add($connector, $connectionId);
                $this->userSessionService->clear();
            }
        }

        return $this->redirectToRoute('dashboard');
    }
}
