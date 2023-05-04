<?php

namespace App\Controller;

use App\Entity\Connector;
use App\Entity\User;
use App\Service\BudgetInsightApiService;
use App\Service\ConnectorService;
use App\Service\TimeSerieService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @method User getUser()
 */
#[Route('/wallet')]
class WalletController extends AbstractController
{
    #[Route('/add/{uuid}', name: 'wallet_add')]
    public function add(Connector $connector, BudgetInsightApiService $api): Response
    {
        $useBearerToken = (bool)$this->getUser()->getBearerToken();

        $temporaryCode = $api->generateTemporaryCode($useBearerToken);

        $redirectUri = $this->generateUrl('api_users_me_webview', [
            'bank_name' => $connector->getName(),
            'connector_uuids' => $connector->getUuid()
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $parameters = [
            'client_id' => $this->getParameter('client_id'),
            'client_secret' => $this->getParameter('client_secret'),
            'redirect_uri' => $redirectUri,
            'connector_uuids' => $connector->getUuid(),
            'code' => $temporaryCode->code
        ];

        $query = http_build_query($parameters);

        $url = "{$this->getParameter('base_url')}/auth/webview/connect?$query";

        return $this->redirect($url);
    }

    #[Route('/', name: 'wallet')]
    public function index(): Response
    {
        return $this->render('wallet/index.html.twig');
    }

    #[Route('/list', name: 'wallet_list')]
    public function list(ConnectorService $connectorRepo): Response
    {
        return $this->render('wallet/list.html.twig', [
            'connectors' => $connectorRepo->findAllIndexedByProducts()
        ]);
    }

    #[Route('/checking', name: 'wallet_checking_list')]
    public function checkingList(): Response
    {
        return $this->render('wallet/checking/index.html.twig');
    }

    #[Route('/checking/{id}', name: 'wallet_checking_view')]
    public function checkingView(int $id, BudgetInsightApiService $api, TimeSerieService $timeSerieService): Response
    {
        $account = $api->getBankAccount($id);

        if (!$account) {
            return $this->redirectToRoute('dashboard');
        }

        $timeSerieService->add($account->id, $account->balance);

        $transactions = $api->listTransactions($id);

        $wordings = [];

        foreach ($transactions as $transaction) {
            $stemmedWording = $transaction->stemmed_wording;

            if (!isset($wordings[$stemmedWording]) && strlen($stemmedWording) < 45) {
                $wordings[$stemmedWording] = $stemmedWording;
            }
        }

        sort($wordings);

        return $this->render('wallet/checking/view.html.twig', [
            'account' => $account,
            'limit' => 10,
            'offset' => 0,
            'transactions' => $transactions,
            'wordings' => $wordings
        ]);
    }

    #[Route('/market', name: 'wallet_market_list')]
    public function marketList(): Response
    {
        return $this->render('wallet/market/index.html.twig');
    }

    #[Route('/market/{id}', name: 'wallet_market_view')]
    public function marketView(int $id, BudgetInsightApiService $api): Response
    {
        $account = $api->getBankAccount($id);

        if (!$account) {
            return $this->redirectToRoute('wallet_market_list');
        }

        return $this->render('wallet/market/view.html.twig', [
            'account' => $api->getBankAccount($id)
        ]);
    }

    #[Route('/loans', name: 'wallet_loans')]
    public function loans(): Response
    {
        return $this->render('wallet/loans/index.html.twig');
    }

    #[Route('/savings', name: 'wallet_savings_list')]
    public function savingsList(): Response
    {
        return $this->render('wallet/savings/list.html.twig');
    }

    #[Route('/savings/{id}', name: 'wallet_savings_view')]
    public function savingsView(int $id, BudgetInsightApiService $api): Response
    {
        return $this->render('wallet/savings/view.html.twig', [
            'account' => $api->getBankAccount($id),
            'transactions' => $api->listTransactions($id)
        ]);
    }
}
