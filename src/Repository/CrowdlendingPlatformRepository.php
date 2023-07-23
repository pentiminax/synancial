<?php

namespace App\Repository;

use App\Entity\CrowdlendingPlatform;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CrowdlendingPlatform>
 *
 * @method CrowdlendingPlatform|null find($id, $lockMode = null, $lockVersion = null)
 * @method CrowdlendingPlatform|null findOneBy(array $criteria, array $orderBy = null)
 * @method CrowdlendingPlatform[]    findAll()
 * @method CrowdlendingPlatform[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CrowdlendingPlatformRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CrowdlendingPlatform::class);
    }

    public function add(CrowdlendingPlatform $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
