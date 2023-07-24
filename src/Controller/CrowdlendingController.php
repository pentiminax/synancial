<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\CrowdlendingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @method User getUser()
 */
class CrowdlendingController extends AbstractController
{
    public function __construct(
        private readonly CrowdlendingService $crowdlendingService
    )
    {
    }

    #[Route('/wallet/crowdlending', name: 'crowdlending_index')]
    public function index(): Response
    {
        return $this->render('crowdlending/index.html.twig',[
            'crowdlendingsIndexedByPlatform' => $this->crowdlendingService->getCrowdlendingsIndexedByPlatform($this->getUser()),
            'totalInvestedAmount' => $this->crowdlendingService->getTotalInvestedAmount($this->getUser()),
        ]);
    }
}