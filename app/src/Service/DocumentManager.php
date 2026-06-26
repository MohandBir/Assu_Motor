<?php

namespace App\Service;

use App\Entity\Document;
use App\Entity\Subscription;
use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class DocumentManager  
{
    public function __construct(
        private string $documentDir,
        private EntityManagerInterface $em,
    ) {}

    /**
     * @param UploadedFile[] $files   Tableau de fichiers téléversés
     */
    public function handleDocument(array $files, Subscription $subscription)
    {
        foreach ($files as $type =>  $file) {
            $newFileName = $type. '_' .uniqid(). '.' .$file->guessExtension();
            
            $file->move($this->documentDir, $newFileName);
            $document = (new Document())
                ->setName($newFileName)
                ->setType($type)
                ->setSubscription($subscription)
                ->setUploadedAt(new DateTimeImmutable('now'))
            ;  
            $this->em->persist($document);   
        }
        
        $subscription->setStatus(Subscription::PENDING_REVIEW);

        return $subscription;
    } 

    public function getDocumentPath(Document $document): string
    {
        $filePath = $this->documentDir. $document->getName();

        return $filePath;
    }
  /**
 * @param Collection<int, Document> $documents
 */
    public function removeFiles(Collection $documents): void
    {
        foreach ($documents as $document) {
            $filePath = $this->documentDir . $document->getName();
            
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            
            $this->em->remove($document);   
        }
    }  
}
