<?php

namespace App\Repository;

use App\Entity\Crowdlending;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Crowdlending>
 *
 * @method Crowdlending|null find($id, $lockMode = null, $lockVersion = null)
 * @method Crowdlending|null findOneBy(array $criteria, array $orderBy = null)
 * @method Crowdlending[]    findAll()
 * @method Crowdlending[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CrowdlendingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Crowdlending::class);
    }

    public function add(Crowdlending $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public  function getCrowdlendingsIndexedByPlatform(): array
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.platform', 'p')
            ->groupBy('c.platform')
            ->getQuery()
            ->getResult();
    }
}
