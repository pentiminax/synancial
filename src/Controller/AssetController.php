<?php

namespace App\Controller;

use App\Entity\Crowdlending;
use App\Form\CrowdlendingType;
use App\Service\CrowdlendingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/assets', name: 'asset_')]
class AssetController extends AbstractController
{
    #[Route('/add', name: 'add')]
    public function add(): Response
    {
        return $this->render('asset/add.html.twig');
    }

    #[Route('/add/crowdlendings', name: 'add_crowdlendings')]
    public function addCrowdlendings(CrowdlendingService $crowdlendingService, Request $request): Response
    {
        $form = $this->createForm(CrowdlendingType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Crowdlending $crowdlending */
            $crowdlending = $form->getData();

            $crowdlendingService->add($crowdlending);

            return $this->redirectToRoute('crowdlending_index');
        }

        return $this->render('asset/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
