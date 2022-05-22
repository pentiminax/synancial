<?php

namespace App\Controller;

use App\Entity\User;
use App\Model\ApiResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User getUser()
 */
class UserController extends AbstractController
{
    #[Route('/user/settings', name: 'user_settings')]
    public function index(): Response
    {
        return $this->render('user/settings.html.twig');
    }

    #[Route('/api/user/secretmode', name: 'user_secretmode', methods: ['PATCH'])]
    public function updateSecretMode(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        $parameters = json_decode($request->getContent(), true);
        $secretmode = $parameters['secretMode'];

        $user->setIsSecretModeEnabled($secretmode);

        $em->flush();

        return $this->json(new ApiResponse(result: ['success']));
    }
}
