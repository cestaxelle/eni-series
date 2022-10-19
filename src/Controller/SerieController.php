<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/series')]
class SerieController extends AbstractController
{
    #[Route('/', name: 'series_list')]
    public function list(): Response
    {
        return $this->render('serie/index.html.twig');
    }

//    je dois passer des requirements sinon à cause de /series/{id}, je ne passerai jamais dans /series/new
    #[Route('/{id}', name: 'series_detail', requirements: ['id' => '\d+'])]
    public function detail(): Response
    {
        return $this->render('serie/detail.html.twig');
    }

    #[Route('/new', name: 'series_new')]
    public function new(): Response
    {
        return $this->render('serie/new.html.twig');
    }
}
