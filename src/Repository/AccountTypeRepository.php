<?php

namespace App\Repository;

use App\Entity\AccountType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AccountType>
 *
 * @method AccountType|null find($id, $lockMode = null, $lockVersion = null)
 * @method AccountType|null findOneBy(array $criteria, array $orderBy = null)
 * @method AccountType[]    findAll()
 * @method AccountType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccountTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccountType::class);
    }

    public function findAllIds(): array
    {
        return $this
            ->createQueryBuilder('a', 'a.id')
            ->select('a.id')
            ->getQuery()->getArrayResult();
    }

    public function add(AccountType $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AccountType $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
