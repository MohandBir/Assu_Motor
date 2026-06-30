<?php

namespace App\Controller;

use App\Entity\Subscription;
use App\Service\PdfGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
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
    public function generate(?Subscription $subscription, PdfGenerator $pdfGenerator): Response
    {
        //middle ware
        if (!$subscription) {
            $this->addFlash('danger', 'Accès non autorisé');
            return$this->redirectToRoute('app_logout');
        }
        $isOwner = $this->getUser() === $subscription->getUser();
        $isAdmin = $this->isGranted('ROLE_ADMIN');

        if ((!$isOwner && !$isAdmin) || ($subscription->getStatus() !== Subscription::VALIDATED)) {
            $this->addFlash('danger', 'Accès non autorisé');

            return$this->redirectToRoute('app_logout'); 
        }

        $fileName = 'attestation_' . $subscription->getReferenceNumber() . '.pdf';
        $filePath = $this->attestationDir . '/' . $fileName;

        if (!file_exists($filePath)) {
            if (!is_dir($this->attestationDir)) {
                mkdir($this->attestationDir, 0755, true);
            }

            $pdfContent = $pdfGenerator->generate('pdf/attestation.html.twig', [
                'subscription' => $subscription
            ]);

            file_put_contents($filePath, $pdfContent);
        }
        
        return new BinaryFileResponse($filePath, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $fileName . '"'
        ]);
    }
}
