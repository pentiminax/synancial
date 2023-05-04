<?php

namespace App\Repository;

use App\Entity\Dividend;
use App\Entity\Symbol;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Symbol>
 *
 * @method Symbol|null find($id, $lockMode = null, $lockVersion = null)
 * @method Symbol|null findOneBy(array $criteria, array $orderBy = null)
 * @method Symbol[]    findAll()
 * @method Symbol[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SymbolRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Symbol::class);
    }

    public function save(Symbol $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Symbol $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Symbol[]
     */
    public function findAllISINThatPayDividends(): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.dividendFrequency != :dividendFrequency')
            ->setParameters([
                'dividendFrequency' => 'none',
            ])
            ->getQuery()
            ->getResult();
    }

    public function findOneThatPayDividendsByISIN(string $isin): ?Symbol
    {
        return $this->createQueryBuilder('s')
            ->where('s.isin = :isin')
            ->andWhere('s.dividendFrequency != :dividendFrequency')
            ->setParameters([
                'dividendFrequency' => 'none',
                'isin' => $isin
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllThatPayDividendsByISINS(array $isins)
    {
        return $this->createQueryBuilder('s')
            ->where('s.isin IN (:isin)')
            ->andWhere('s.dividendFrequency != :dividendFrequency')
            ->setParameters([
                'dividendFrequency' => 'none',
                'isin' => $isins
            ])
            ->getQuery()
            ->getResult();
    }
}
