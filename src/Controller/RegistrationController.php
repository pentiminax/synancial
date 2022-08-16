<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Service\BudgetInsightApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'register')]
    public function register(Request $request, BudgetInsightApiService $budgetInsightApiService, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $em): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($userPasswordHasher->hashPassword($user, $form->get('plainPassword')->getData()));

            $temporaryCode = $budgetInsightApiService->generateTemporaryCode();

            $bearerToken = $budgetInsightApiService->generatePermanentUserAccessToken($temporaryCode->code);

            $user->setBearerToken($bearerToken->access_token);

            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('dashboard');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
