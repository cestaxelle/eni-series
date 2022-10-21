<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Repository\SerieRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/test')]
class TestController extends AbstractController
{
    #[Route('/', name: 'app_test')]
    public function index(): Response
    {
        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }

    #[Route('/create')]
    public function create(ManagerRegistry $doctrine): Response
    {
        // obsolète : $entityManager = $this->getDoctrine()->getManager();
        $entityManager = $doctrine->getManager();

        $you = new Serie();
        $you->setName('You');
        $you->setOverview('Série pour les fans de Gossip Girl qui veulent revoir Penn Badgley');
        $you->setStatus('ended');
        $you->setVote(7.7);
        $you->setPopularity(117);
        $you->setGenres('Policier / Drame / Romantique');
        $you->setFirstAirDate(new \DateTime('2018-09-09'));
        $you->setLastAirDate(new \DateTime('2022-10-20'));
        $you->setBackdrop('you.jpg');
        $you->setPoster('you.jpg');
        $you->setTmdbId(78191);
        $you->setDateCreated(new \DateTime());

        $entityManager->persist($you);
        $entityManager->flush();
        // ou $entityManager->getRepository(Serie::class)->save($you, true);

        return new Response("La série You a été créée");
    }

    #[Route('/update')]
    public function update(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();

        // récupérer le repo de l'entité Serie : 2 syntaxes possibles :
        // $serieRepository = $entityManager->getRepository('App\Entity\Serie');
        $serieRepository = $entityManager->getRepository(Serie::class);

        // récupérer la série You : 2 syntaxes possibles
        // attention, c'est une requête SQL mais on est bien en PHP, on écrit les noms des colonnes en camelCase, conformément aux attributs
        $you = $serieRepository->findOneBy(['name' => 'You']); // on peut passer plusieurs paramètres ici pour un WHERE name='truc' AND genres='machin', AND pas OR
        // $you = $serieRepository->findOneByName('You'); // PHPStorm ne connaît pas cette méthode

        // modifier la série
        $you->setOverview('Blurb série You');

        // pas besoin de persist() quand on modifie une ligne existante
        $entityManager->flush();

        return new Response('La série You a été modifiée');

    }

    #[Route('/delete')]
    public function delete(SerieRepository $serieRepository): Response
    {
        $you = $serieRepository->findOneByName('you');

        if ($you != null) {
            // nouveau : on peut passer directement par le repo pour remove
            $serieRepository->remove($you, true);
//          sinon avant il fallait récupérer l'entityManager :
//          $entityManager->remove($you);
//          $entityManager->flush();

            $response = new Response('La série You a été supprimée');
        } else {
            $response = new Response('La série You n\'existe pas en base de données');
        }

//        ou
//        try {
//            $serieRepository->remove($you, true);
//        } catch (\TypeError) {
//            $response = new Response('La série You n\'existe pas en base de données');
//        }

        return $response;

    }
}
