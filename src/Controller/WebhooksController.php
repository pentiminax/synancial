<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WebhooksController extends AbstractController
{
    #[Route('/webhooks/connection_synced', name: 'app_webhooks')]
    public function connectionSynced(Request $request, LoggerInterface $logger): Response
    {
        $params = $request->request->all();

        $logger->info('WEBHOOKS', [json_encode($params)]);

        return $this->json('');
    }
}
