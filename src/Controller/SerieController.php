<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Form\SerieType;
use App\Repository\SerieRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
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
//        $series = $serieRepository->findAllBetweenDates(new \DateTime('2019-01-01'), new \DateTime('2019-12-31'));
        $series = $serieRepository->findAllWithSeasons();

        return $this->render('serie/index.html.twig', [
            'series' => $series
        ]);
    }

//    je dois passer des requirements sinon à cause de /series/{id}, je ne passerai jamais dans /series/new
    #[Route('/{id}', name: 'series_detail', requirements: ['id' => '\d+'])]
    public function detail($id, SerieRepository $serieRepository, Request $request): Response
    {
        $serie = $serieRepository->find($id);
        $idtest = $request->get("id");
        return $this->render('serie/detail.html.twig', [
        'serie' => $serie,
            'idtest' => $idtest
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

    #[IsGranted('ROLE_ADMIN')]  // pour bloquer cette route aux non-admin
    #[Route('/new', name: 'series_new')]
    public function new(Request $request, EntityManagerInterface $em, FileUploader $fileUploader): Response
    {
        $serie = new Serie();
        $serie->setDateCreated(new \DateTime()); // ou alors utiliser les LifeCycleCallbacks de Doctrine
        $serieForm = $this->createForm(SerieType::class, $serie);

        $serieForm->handleRequest($request);    // récupère ce qu'il y a dans le form et le met dans $serie

        // je vérifie si l'utilisateur est en train d'envoyer le formulaire ou s'il faut juste l'afficher
        if ($serieForm->isSubmitted() && $serieForm->isValid()) {
            // $this->denyAccessUnlessGranted('ROLE_ADMIN');   // l'utilisateur verra le formulaire mais ne pourra pas le valider s'il n'est pas admin

            // uploader les images
            /** @var UploadedFile $backdropImage */                         // getData peut tout renvoyer (objet, string, nombre...), donc on type avec @var
            $backdropImage = $serieForm->get('backdropFile')->getData();    // utiliser le name de l'input dans le formulaire
            if ($backdropImage) {
                $backdrop = $fileUploader->upload($backdropImage, '/backdrops');
                $serie->setBackdrop($backdrop);
            }

            /** @var UploadedFile $posterImage */
            $posterImage = $serieForm->get('posterFile')->getData();
            if ($posterImage) {
                $poster = $fileUploader->upload($posterImage, '/posters/series');
                $serie->setPoster($poster);
            }

            // s'il est en train d'envoyer le form, j'enregistre la nouvelle série en BDD
            $em->persist($serie);
            $em->flush();

            $this->addFlash('success', 'La série a bien été créée !');

            // et je redirige l'ut vers la liste des séries
            return $this->redirectToRoute('series_list');
        }

        return $this->render('serie/new.html.twig', [
            'serieForm' => $serieForm->createView()
        ]);
    }
}
