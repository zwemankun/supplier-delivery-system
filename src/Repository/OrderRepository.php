<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }
  
    /**
     * Find active orders (not soft deleted)
     */
    public function findActiveOrders()
    {
        return $this->createQueryBuilder('o')
            ->where('o.deleted_at IS NULL')
            ->orderBy('o.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find orders by status
     */
    public function findByStatus(string $status)
    {
        return $this->createQueryBuilder('o')
            ->where('o.status = :status')
            ->andWhere('o.deleted_at IS NULL')
            ->setParameter('status', $status)
            ->orderBy('o.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find orders by customer ID
     */
    public function findByCustomerId(int $customerId)
    {
        return $this->createQueryBuilder('o')
            ->where('o.customer_id = :customerId')
            ->andWhere('o.deleted_at IS NULL')
            ->setParameter('customerId', $customerId)
            ->orderBy('o.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get order statistics
     */
    public function getOrderStatistics()
    {
        return $this->createQueryBuilder('o')
            ->select('o.status, COUNT(o.id) as count, SUM(o.total_amount) as total')
            ->where('o.deleted_at IS NULL')
            ->groupBy('o.status')
            ->getQuery()
            ->getResult();
    }

    /**
     * Soft delete an order
     */
    public function softDelete(Order $order): void
    {
        $order->setDeletedAt(new \DateTimeImmutable());
        $this->getEntityManager()->flush();
    }

    /**
     * Hard delete an order
     */
    public function hardDelete(Order $order): void
    {
        $this->getEntityManager()->remove($order);
        $this->getEntityManager()->flush();
    }

    /**
     * Get recent orders
     */
    public function getRecentOrders(int $limit = 5)
    {
        return $this->createQueryBuilder('o')
            ->where('o.deleted_at IS NULL')
            ->orderBy('o.created_at', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
 
}
