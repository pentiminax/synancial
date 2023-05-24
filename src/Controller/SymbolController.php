<?php

namespace App\Controller;

use App\Service\SymbolService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SymbolController extends AbstractController
{
    #[Route('/symbols', name: 'stocks')]
    public function index(SymbolService $symbolService): Response
    {
        return $this->render('symbol/index.html.twig', [
            'symbols' => $symbolService->findAllWithLimit(25)
        ]);
    }
}