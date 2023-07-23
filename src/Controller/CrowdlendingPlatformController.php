<?php

namespace App\Controller;

use App\Entity\CrowdlendingPlatform;
use App\Repository\CrowdlendingPlatformRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class CrowdlendingPlatformController extends AbstractController
{
    public function __construct(
        private readonly CrowdlendingPlatformRepository $crowdlendingPlatformRepo,
        private readonly SerializerInterface $serializer
    )
    {
    }

    #[Route('/crowdlending_platform', name: 'crowdlending_platform_add')]
    #[Route('/', methods: ['POST'])]
    public function add(Request $request): Response
    {
        /** @var   CrowdlendingPlatform $crowdlendingPlatform */
        $crowdlendingPlatform = $this->serializer->deserialize($request->getContent(), CrowdlendingPlatform::class, 'json');

        $this->crowdlendingPlatformRepo->add($crowdlendingPlatform, true);

        return $this->json([
            'error' => false,
            'result' => $crowdlendingPlatform->getId()
        ]);
    }
}