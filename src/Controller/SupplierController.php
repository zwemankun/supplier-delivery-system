<?php

namespace App\Controller;

use App\Entity\Supplier;
use App\Form\SupplierType;
use App\Form\SupplierTypeForm;
use App\Repository\SupplierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
#[Route('/supplier')]
class SupplierController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private SupplierRepository   $repo
    ) {}
    #[Route('/', name: 'app_supplier', methods: ['GET'])]
    public function index(Request $request): Response
    {
        // Grab raw “fromDate” / “toDate” GET params (empty string if missing)
        $fromDate = $request->query->get('fromDate', '');
        $toDate   = $request->query->get('toDate', '');

        // Convert to DateTime or null
        try {
            $from = $fromDate !== '' ? new \DateTimeImmutable($fromDate) : null;
            $to   = $toDate   !== '' ? new \DateTimeImmutable($toDate)   : null;
        } catch (\Exception $e) {
            $this->addFlash('error', 'Invalid date format');
            $from = $to = null;
        }

        // Fetch suppliers, filtered if dates provided
        $suppliers = $this->repo->findByCreatedAtBetween($from, $to);

        return $this->render('supplier/index.html.twig', [
            'suppliers' => $suppliers,
            'fromDate'  => $fromDate,
            'toDate'    => $toDate,
        ]);
    }
    #[Route('/new', name: 'app_supplier_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $supplier = new Supplier();
        $form = $this->createForm(SupplierTypeForm::class, $supplier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $supplier->setCreatedAt(new \DateTimeImmutable());
            $entityManager->persist($supplier);
            $entityManager->flush();

            return $this->redirectToRoute('app_supplier', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('supplier/new.html.twig', [
            'supplier' => $supplier,
            'form' => $form,
        ]);
    }
     
    #[Route('/{id}/edit', name: 'app_supplier_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Supplier $supplier): Response
    {
        // Make sure we're using the correct form class name
        $form = $this->createForm(SupplierTypeForm::class, $supplier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $supplier->setUpdatedAt(new \DateTimeImmutable());
            $this->em->flush();

            return $this->redirectToRoute('app_supplier', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('supplier/edit.html.twig', [
            'supplier' => $supplier,
            'form' => $form,
        ]);
    }
    #[Route('/{id}', name: 'app_supplier_delete', methods: ['POST'])]
    public function delete(Request $request, Supplier $supplier): Response
    {
         
            $this->em->remove($supplier);
            $this->em->flush();
            return $this->redirectToRoute('app_supplier');
    }
    
}

