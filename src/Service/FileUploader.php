<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{

    public function __construct(private string $targetDirectory, private SluggerInterface $slugger)
    {
    }

    public function upload(UploadedFile $file, string $directory): string
    {
        // ex : Mon Fichier.jpg
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);    // >> Mon Fichier
        $safeFilename = $this->slugger->slug($originalFilename);    // pas d'espaces ni de caractères spéciaux dans le nom grâce au slugger >> mon-fichier
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();    // ajout d'une identifiant unique au nom du fichier >> mon-fichier-z1r2563arz.jpg

        try {
            $file->move($this->getTargetDirectory() . $directory, $fileName);    // concat pour avoir le bon répertoire, si on n'a pas les droits pour déplacer le fichier, on aura une erreur
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        return $fileName;
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }



}