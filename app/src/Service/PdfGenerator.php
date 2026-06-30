<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
use Twig\Environment;

class PdfGenerator  
{
    public function __construct(
        private Environment $twig,
        private string $publicDir,
    ) {}

    public function generate(string $template, array $data = []): string
    {
        $options = new Options();
        $options->set('defaultFont', 'Helvetica');
        $options->set('isRemoteEnabled', true);  // pour charger des images externes
        $options->set('chroot', $this->publicDir);
        $options->set('isHtml5ParserEnabled', true);
        
        $dompdf = new Dompdf($options);

        // Rendre le template Twig en HTML
        $html = $this->twig->render($template, $data);

        // Charger l'HTML dans Dompdf
        $dompdf->loadHtml($html);

        // Définir le format de papier (...)
        $dompdf->setPaper('A4', 'portrait');

        // Générer le PDF
        $dompdf->render();

        // Retourner le contenu binaire du PDF
        return $dompdf->output();

    }
 
}
