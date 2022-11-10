<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ConnectionRepository;
use App\Repository\ConnectorRepository;
use App\Service\BudgetInsightApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @method User getUser()
 */
class DocumentController extends AbstractController
{
    #[Route('/documents', name: 'documents')]
    public function index(BudgetInsightApiService $apiService, ConnectionRepository $connectionRepo, ConnectorRepository $connectorRepo): Response
    {
        $connections = $connectionRepo->findAllIndexedById($this->getUser());

        $subscriptions = $apiService->listSubscriptions();
        $documents = $apiService->listDocuments();

        $data = [];

        foreach ($subscriptions as $subscription) {
            $data[$subscription->id]['connector'] = $connections[$subscription->id_connection]->getConnector();
            foreach ($documents as $document) {
                if ($document->id_subscription === $subscription->id) {
                    $data[$subscription->id]['documents'][] = $document;
                }
            }
        }

        return $this->render('documents/index.html.twig', [
            'subscriptions' => $data
        ]);
    }

    #[Route('/documents/add', name: 'documents_add')]
    public function add(BudgetInsightApiService $apiService): Response
    {
        $bearerToken = $this->getUser()->getBearerToken();

        $url = $apiService->getWebviewUrl($bearerToken, 'document');

        return $this->redirect($url);
    }

    #[Route('/documents/{idDocument}/file/{webid}', name: 'documents_download_file')]
    public function downloadFile(BudgetInsightApiService $apiService, int $idDocument, string $webid): Response
    {
        $content = $apiService->getDocumentFile($idDocument, $webid);

        $tempFilename = tempnam(sys_get_temp_dir(), 'Synancial');

        file_put_contents($tempFilename, $content);

        $response = new BinaryFileResponse($tempFilename);
        $response->deleteFileAfterSend();
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, "$webid.pdf");

        return $response;
    }

    #[Route('/thumbnail/{idDocument}/{webid}', name: 'documents_thumbnail')]
    public function thumbnail(BudgetInsightApiService $apiService, int $idDocument, string $webid): Response
    {
        return (new Response())->setContent($apiService->getDocumentThumbnail($idDocument, $webid));
    }
}
