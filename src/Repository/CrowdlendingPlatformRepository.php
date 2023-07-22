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

//    /**
//     * @return CrowdlendingPlatform[] Returns an array of CrowdlendingPlatform objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CrowdlendingPlatform
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
