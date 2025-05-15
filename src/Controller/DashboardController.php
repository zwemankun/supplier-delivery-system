<?php
// src/Controller/DashboardController.php
namespace App\Controller;

use App\Repository\CustomerRepository;
use App\Repository\DeliveryRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\SupplierRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(
        SupplierRepository $supplierRepository,
        ProductRepository $productRepository,
        OrderRepository $orderRepository,
        DeliveryRepository $deliveryRepository
    ): Response {
        // Get counts for dashboard cards
        $supplierCount = $supplierRepository->count([]);
        $productCount = $productRepository->count([]);
        $pendingOrderCount = $orderRepository->count(['status' => 'pending']);
        $deliveryCount = $deliveryRepository->count([]);

        return $this->render('dashboard/index.html.twig', [
            'supplierCount' => $supplierCount,
            'productCount' => $productCount,
            'pendingOrderCount' => $pendingOrderCount,
            'deliveryCount' => $deliveryCount,
        ]);
    }
}