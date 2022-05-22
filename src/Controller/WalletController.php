<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ConnectorRepository;
use App\Service\BudgetInsightApiService;
use App\Service\TimeSerieService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @method User getUser()
 */
class WalletController extends AbstractController
{
    #[Route('/wallet', name: 'wallet')]
    public function index(): Response
    {
        return $this->render('wallet/index.html.twig');
    }

    #[Route('/wallet/list', name: 'wallet_list')]
    public function list(ConnectorRepository $connectorRepo): Response
    {
        $connectors = $connectorRepo->findAll();

        return $this->render('wallet/add.html.twig', [
            'connectors' => $connectors
        ]);
    }

    #[Route('/wallet/checking', name: 'wallet_checking_list')]
    public function checkingList(): Response
    {
        return $this->render('wallet/checking/index.html.twig');
    }

    #[Route('/wallet/checking/{id}', name: 'wallet_checking_view')]
    public function checkingView(int $id, BudgetInsightApiService $api, TimeSerieService $timeSerieService): Response
    {
        $account = $api->getBankAccount($id);

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

        return $this->render('wallet/checking_view.html.twig', [
            'account' => $account,
            'transactions' => $transactions,
            'wordings' => $wordings
        ]);
    }

    #[Route('/wallet/market', name: 'wallet_market')]
    public function market(): Response
    {
        return $this->render('wallet/market.html.twig');
    }

    #[Route('/wallet/add/{uuid}', name: 'wallet_add')]
    public function add(string $uuid, BudgetInsightApiService $api): Response
    {
        $bearerCode = $this->getUser()->getBearerToken();

        $temporaryCode = $api->generateTemporaryCode($bearerCode);

        $parameters = [
            'client_id' => $this->getParameter('client_id'),
            'client_secret' => $this->getParameter('client_secret'),
            'redirect_uri' =>  $this->generateUrl('api_users_me_webview', referenceType: UrlGeneratorInterface::ABSOLUTE_URL),
            'connector_uuids' => $uuid,
            'code' => $temporaryCode->code
        ];

        $query = http_build_query($parameters);

        $url = "{$this->getParameter('base_url')}/auth/webview/connect?$query";

        return $this->redirect($url);
    }
}
