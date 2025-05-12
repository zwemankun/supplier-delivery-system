<?php

namespace App\Repository;

use App\Entity\Supplier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Supplier>
 *
 * @method Supplier|null find($id, $lockMode = null, $lockVersion = null)
 * @method Supplier|null findOneBy(array $criteria, array $orderBy = null)
 * @method Supplier[]    findAll()
 * @method Supplier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SupplierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Supplier::class);
    }

    /**
     * Find active suppliers (not soft-deleted)
     */
    public function findActive(): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.deleted_at IS NULL')
            ->orderBy('s.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find suppliers by date range
     */
    public function findByCreatedAtBetween(
        ?\DateTimeImmutable $fromDate,
        ?\DateTimeImmutable $toDate
    ): array {
        $qb = $this->createQueryBuilder('s')
            // only non-deleted
            ->andWhere('s.deleted_at IS NULL');
    
        if ($fromDate) {
            // ensure start of day
            $fromDate = $fromDate->setTime(0, 0, 0);
            $qb->andWhere('s.created_at >= :fromDate')
               ->setParameter('fromDate', $fromDate, \Doctrine\DBAL\Types\Types::DATETIME_IMMUTABLE);
        }
    
        if ($toDate) {
            // ensure end of day
            $toDate = $toDate->setTime(23, 59, 59);
            $qb->andWhere('s.created_at <= :toDate')
               ->setParameter('toDate', $toDate, \Doctrine\DBAL\Types\Types::DATETIME_IMMUTABLE);
        }
    
        return $qb
            ->orderBy('s.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }
}