<?php

namespace App\Service;

use App\Entity\Language;
use App\Entity\User;
use App\Model\UserAccountData;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;

class UserService
{
    public function __construct(
        private EntityManagerInterface $em,
        private Security $security,
        private SerializerInterface $serializer
    )
    {
    }

    public function updateUser(string $data): void
    {
        /** @var User $user */
        $user = $this->security->getUser();

        /** @var UserAccountData $userAccountData */
        $userAccountData = $this->serializer->deserialize($data, UserAccountData::class, 'json');

        $language = $this->em->getPartialReference(Language::class, $userAccountData->getLanguage());

        $user->setEmail($userAccountData->getEmail());
        $user->setFirstname($userAccountData->getFirstname());
        $user->setLastname($userAccountData->getLastname());
        $user->setLanguage($language);

        $this->em->flush();
    }
}