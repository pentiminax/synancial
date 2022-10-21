<?php

namespace App\Controller;

use App\Model\ApiResponse;
use App\Service\FortuneoApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'dashboard')]
    public function index(): Response
    {
        return $this->render('dashboard/index.html.twig');
    }

    #[Route('/about', name: 'about')]
    public function about(): Response
    {
        return $this->redirect('https://pentiminax.fr');
    }

    #[Route('/api/fortuneo/news', name: 'dashboard_fortuneo_news')]
    public function listFortuneoNews(FortuneoApiService $fortuneoApiService): Response
    {
        return $this->json(new ApiResponse(result: $fortuneoApiService->listNews()));
    }
}
