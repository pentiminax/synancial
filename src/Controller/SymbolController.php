<?php

namespace App\Controller;

use App\Entity\Symbol;
use App\Service\SymbolService;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SymbolController extends AbstractController
{
    #[Route('/symbols', name: 'app_symbol_index')]
    public function index(SymbolService $symbolService): Response
    {
        return $this->render('symbol/index.html.twig', [
            'symbols' => $symbolService->findAllWithLimit(25)
        ]);
    }

    #[Route('/symbols/{symbol}', name: 'app_symbol_show')]
    public function show(
         #[MapEntity(expr: 'repository.findOneBySymbol(symbol)')]
         Symbol $symbol
    ): Response {
        return $this->render('symbol/show.html.twig', [
            'symbol' => $symbol
        ]);
    }
}