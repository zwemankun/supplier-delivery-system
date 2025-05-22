<?php

namespace App\Controller;

use App\Entity\Delivery;
use App\Form\DeliveryType;
use App\Form\DeliveryTypeForm;
use App\Repository\DeliveryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/delivery')]
class DeliveryController extends AbstractController
{
    #[Route('/', name: 'app_delivery', methods: ['GET'])]
    public function index(DeliveryRepository $deliveryRepository): Response
    {
        return $this->render('delivery/index.html.twig', [
            'deliveries' => $deliveryRepository->findAllActive(),
        ]);
    }

    #[Route('/new', name: 'app_delivery_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $delivery = new Delivery();
        $form = $this->createForm(DeliveryTypeForm::class, $delivery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($delivery);
            $entityManager->flush();

         
            return $this->redirectToRoute('app_delivery', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('delivery/new.html.twig', [
            'delivery' => $delivery,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_delivery_show', methods: ['GET'])]
    public function show(Delivery $delivery): Response
    {
        return $this->render('delivery/show.html.twig', [
            'delivery' => $delivery,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_delivery_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Delivery $delivery, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DeliveryTypeForm::class, $delivery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            
            return $this->redirectToRoute('app_delivery', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('delivery/edit.html.twig', [
            'delivery' => $delivery,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_delivery_delete', methods: ['POST'])]
    public function delete(Request $request, Delivery $delivery, EntityManagerInterface $entityManager): Response
    {
         
            // Soft delete - set the deleted_at timestamp
           // $delivery->setDeletedAt(new \DateTimeImmutable());
            $entityManager->remove($delivery);
            $entityManager->flush();
            return $this->redirectToRoute('app_delivery', [], Response::HTTP_SEE_OTHER);
    }
    
}