<?php

namespace App\Controller;

use App\Entity\Subscription;
use App\Service\PdfGenereator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AttestationController extends AbstractController
{
    public function __construct
    (
        private string $attestationDir,
    )
    {}
    #[Route('/attestation/subscription/{id}', name: 'app_attestation_generate')]
    public function generate(?Subscription $subscription, PdfGenereator $pdfGenereator): Response
    {
        //middle ware
        if (!$subscription) {
            $this->addFlash('danger', 'Accès non autorisé');
            return$this->redirectToRoute('app_logout');
        }
        if ($this->getUser() !== $subscription->getUser() && !$this->isGranted('ROLE_ADMIN') ) {
            $this->addFlash('danger', 'Accès non autorisé');
            return$this->redirectToRoute('app_logout'); 
        }
        if ($subscription->getStatus() !== Subscription::VALIDATED) {
            $this->addFlash('danger', 'Accès non autorisé');
            return$this->redirectToRoute('app_logout');
        }

        $fileName = 'attestation_' . $subscription->getReferenceNumber() . '.pdf';
        $filePath = $this->attestationDir . '/' . $fileName;

        if (!file_exists($filePath)) {
            
            $pdfContent = $pdfGenereator->generate('pdf/attestation.html.twig', [
                'subscription' => $subscription
            ]);

            file_put_contents($filePath, $pdfContent);

        }
        
        return new Response($filePath, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename"' . $fileName . '"'
        ]);
    }
}
