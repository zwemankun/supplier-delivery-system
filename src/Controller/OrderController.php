<?php

namespace App\Controller;

 
use App\Entity\Order;
use App\Entity\Customer;
use App\Form\OrderTypeForm;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/order')]
final class OrderController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private OrderRepository $orderRepository
    ) {}
    #[Route('/', name: 'app_order')]
    public function index(Request $request): Response
    {
        $status = $request->query->get('status');
        $search = $request->query->get('search');

        if ($status) {
            $orders = $this->orderRepository->findByStatus($status);
        } else {
            $orders = $this->orderRepository->findActiveOrders();
        }

        // Filter by search term if provided
        if ($search) {
            $orders = array_filter($orders, function($order) use ($search) {
                return strpos(strtolower($order->getId()), strtolower($search)) !== false ||
                       strpos(strtolower($order->getCustomerId()), strtolower($search)) !== false;
            });
        }

        $statistics = $this->orderRepository->getOrderStatistics();

        return $this->render('order/index.html.twig', [
            'orders' => $orders,
            'statistics' => $statistics,
            'current_status' => $status,
            'search_term' => $search,
        ]);
    }
     #[Route('/show/{id}', name: 'app_order_show', methods: ['GET'])]
    public function show(Order $order): Response
    {
        if ($order->getDeletedAt()) {
            throw $this->createNotFoundException('Order not found.');
        }

        return $this->render('order/show.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/new', name: 'app_order_new', methods: ['GET', 'POST'])]
      public function new(Request $request): Response
    {
        $order = new Order();
        $form = $this->createForm(OrderTypeForm::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Get the selected customer entity from the form
            $customer = $form->get('customer')->getData();
            
            // Set the customer_id in the order entity
            $order->setCustomerId($customer->getId());
            
            // Set created_at timestamp
            $order->setCreatedAt(new \DateTimeImmutable());

            // Persist and flush
            $this->entityManager->persist($order);
            $this->entityManager->flush();

            $this->addFlash('success', 'Order created successfully!');

            return $this->redirectToRoute('app_order');
        }

        return $this->render('order/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'app_order_edit', methods: ['GET', 'POST'])]
     public function edit(Request $request, Order $order): Response
    {
        // Find the customer entity using the customer_id from the order
        $customer = $this->entityManager
            ->getRepository(Customer::class)
            ->find($order->getCustomerId());
        
        $form = $this->createForm(OrderTypeForm::class, $order, [
            'is_edit' => true
        ]);
        
        // Set the customer field with the found customer entity
        $form->get('customer')->setData($customer);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Get the potentially new selected customer
            $customer = $form->get('customer')->getData();
            
            // Update the customer_id
            $order->setCustomerId($customer->getId());
            
            // Update and flush
            $this->entityManager->flush();

            $this->addFlash('success', 'Order updated successfully!');

            return $this->redirectToRoute('app_order_show', ['id' => $order->getId()]);
        }

        return $this->render('order/edit.html.twig', [
            'form' => $form->createView(),
            'order' => $order
        ]);
    }

    #[Route('/delete/{id}', name: 'app_order_delete', methods: ['POST'])]
    public function delete(Order $order): Response
    {
        $this->orderRepository->hardDelete($order);

        $this->addFlash('success', 'Order deleted successfully!');

        return $this->redirectToRoute('app_order');
    }
    #[Route('/status/{id}', name: 'order_update_status', methods: ['POST'])]
    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        if ($order->getDeletedAt()) {
            return new JsonResponse(['error' => 'Order not found'], 404);
        }

        $status = $request->request->get('status');
        $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

        if (!in_array($status, $validStatuses)) {
            return new JsonResponse(['error' => 'Invalid status'], 400);
        }

        $order->setStatus($status);
        $order->setUpdatedAt(new \DateTimeImmutable());
        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'status' => $status,
           // 'badge_class' => $order->getStatusBadgeClass()
        ]);
    }

    #[Route('/api/statistics', name: 'order_api_statistics', methods: ['GET'])]
    public function getStatistics(): JsonResponse
    {
        $statistics = $this->orderRepository->getOrderStatistics();
        
        $formattedStats = [];
        $total = 0;
        $totalAmount = 0;

        foreach ($statistics as $stat) {
            $formattedStats[$stat['status']] = [
                'count' => $stat['count'],
                'total' => $stat['total']
            ];
            $total += $stat['count'];
            $totalAmount += $stat['total'];
        }

        return new JsonResponse([
            'statistics' => $formattedStats,
            'total_orders' => $total,
            'total_amount' => $totalAmount
        ]);
    }
}
