<?php

namespace App\Repository;

use App\Entity\Delivery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Delivery>
 */
class DeliveryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Delivery::class);
    }

    /**
     * @return Delivery[] Returns an array of active deliveries
     */
    public function findAllActive(): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.deleted_at IS NULL')
            ->orderBy('d.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByTrackingNumber(string $tracking): ?Delivery
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.tracking_number = :tracking')
            ->andWhere('d.deleted_at IS NULL')
            ->setParameter('tracking', $tracking)
            ->getQuery()
            ->getOneOrNullResult();
    }
    
    public function findByOrderId(string $orderId): ?Delivery
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.order_id = :orderId')
            ->andWhere('d.deleted_at IS NULL')
            ->setParameter('orderId', $orderId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}