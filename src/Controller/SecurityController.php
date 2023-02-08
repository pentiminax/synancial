<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use App\Service\BudgetInsightApiService;
use App\Service\CurrencyService;
use App\Service\LanguageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'security_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('dashboard');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    #[Route('/register', name: 'security_register')]
    public function register(
        BudgetInsightApiService $api,
        CurrencyService $currencyService,
        EntityManagerInterface $em,
        LanguageService $languageService,
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher
    ): Response
    {
        $user = new User();

        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($userPasswordHasher->hashPassword($user, $form->get('plainPassword')->getData()));

            $temporaryCode = $api->generateTemporaryCode();
            $bearerToken = $api->generatePermanentUserAccessToken($temporaryCode->code);

            $user->setBearerToken($bearerToken->access_token);
            $user->setCurrency($currencyService->getDefaultCurrency());
            $user->setLanguage($languageService->getDefaultLanguage());

            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('dashboard');
        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/logout', name: 'security_logout')]
    public function logout()
    {

    }
}