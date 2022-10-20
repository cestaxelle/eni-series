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
        $series = [
            [
                'id' => 1,
                'title' => 'Corporate'
            ],
            [
                'id' => 2,
                'title' => 'The IT Crowd'
            ]
        ];

        return $this->render('serie/index.html.twig', [
            'series' => $series
        ]);
    }

//    je dois passer des requirements sinon à cause de /series/{id}, je ne passerai jamais dans /series/new
    #[Route('/{id}', name: 'series_detail', requirements: ['id' => '\d+'])]
    public function detail($id): Response
    {
        // TODO: Récupérer la série à afficher en BDD
        return $this->render('serie/detail.html.twig', [
        'id' => $id
        ]);
    }

    #[Route('/new', name: 'series_new')]
    public function new(): Response
    {
        return $this->render('serie/new.html.twig');
    }
}
