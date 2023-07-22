<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @method User getUser()
 */
class CrowdlendingController extends AbstractController
{
    #[Route('/wallet/crowdlending', name: 'crowdlending_index')]
    public function index(): Response
    {
        return $this->render('crowdlending/index.html.twig',[
            'crowdlendings' => $this->getUser()->getCrowdlendings()
        ]);
    }
}