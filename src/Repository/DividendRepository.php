<?php

namespace App\Repository;

use App\Entity\Dividend;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Dividend>
 *
 * @method Dividend|null find($id, $lockMode = null, $lockVersion = null)
 * @method Dividend|null findOneBy(array $criteria, array $orderBy = null)
 * @method Dividend[]    findAll()
 * @method Dividend[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DividendRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dividend::class);
    }

    public function save(Dividend $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Dividend $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Dividend[]
     */
    public function findAllBySymbolsForCurrentYear(array $symbols): array
    {
        $now = new \DateTime();

        return $this->createQueryBuilder('d')
            ->where('d.symbol IN (:symbols)')
            ->andWhere('YEAR(d.payDate) = :currentYear')
            ->setParameters([
                'currentYear' => $now->format('Y'),
                'symbols' => $symbols
            ])
            ->orderBy('d.payDate')
            ->getQuery()
            ->getResult();
    }
}
