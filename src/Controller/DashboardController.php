<?php

namespace App\Controller;

use App\Service\BudgetInsightApiService;
use App\Service\FortuneoApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'dashboard')]
    public function index(FortuneoApiService $fortuneoApiService, BudgetInsightApiService $budgetInsightApiService): Response
    {
        return $this->render('dashboard/index.html.twig', [
            'news' => $fortuneoApiService->listNews()
        ]);
    }

    #[Route('/about', name: 'about')]
    public function about(): Response
    {
        return $this->redirect('https://pentiminax.fr');
    }
}
