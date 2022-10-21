<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Repository\SerieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/series')]
class SerieController extends AbstractController
{
    #[Route('/', name: 'series_list')]
    public function list(SerieRepository $serieRepository): Response
    {
        // je récupère les données dans le repo
        // premier paramètre du findBy = where, deuxième = order by
//        $series = $serieRepository->findBy([], ['firstAirDate' => 'DESC', 'name' => 'ASC']);
        $series = $serieRepository->findAllBetweenDates(new \DateTime('2019-01-01'), new \DateTime('2019-12-31'));

        return $this->render('serie/index.html.twig', [
            'series' => $series
        ]);
    }

//    je dois passer des requirements sinon à cause de /series/{id}, je ne passerai jamais dans /series/new
    #[Route('/{id}', name: 'series_detail', requirements: ['id' => '\d+'])]
    public function detail($id, SerieRepository $serieRepository): Response
    {
        $serie = $serieRepository->find($id);
        return $this->render('serie/detail.html.twig', [
        'serie' => $serie
        ]);
    }

//    plus rapide :
//    #[Route('/{id}', name: 'series_detail', requirements: ['id' => '\d+'])]
//    public function detail(Serie $serie): Response
//    {
//        return $this->render('serie/detail.html.twig', [
//            'serie' => $serie
//        ]);
//    }

    #[Route('/new', name: 'series_new')]
    public function new(): Response
    {
        return $this->render('serie/new.html.twig');
    }
}
