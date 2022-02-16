<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Form\SerieType;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;


class SerieController extends AbstractController
{
    /**
     * @Route("/series", name="serie_list")
     */
    public function list(SerieRepository $serieRepository): Response
    {
        //$series = $serieRepository->findBy([],['popularity'=> 'DESC', 'vote' => 'DESC'],30,5 );
        $series = $serieRepository->findBestSeries();

        return $this->render('serie/list.html.twig', [
        "series" => $series
        ]);
    }

    /**
     * @Route("/series/details/{id}", name="serie_details")
     */
    public function details(int $id,SerieRepository $serieRepository): Response
    {
        $serie = $serieRepository->findOneBy(['id' => $id]);

        return $this->render('serie/details.html.twig',[
            'serie' => $serie
        ]);
    }

    /**
     * @Route("/series/create", name="serie_create")
     */
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $serie = new Serie();
        $serie->setDateCreated(new \DateTime());

        $serieForm = $this->createForm(SerieType::class,$serie);

        $serieForm->handleRequest($request);//sert à hydrater les données dans l'antité

        if($serieForm->isSubmitted() && $serieForm->isValid())
        {
            $entityManager->persist($serie);// récupérera l'iD en meme temps et l'insert dans $serie
            $entityManager->flush();

            $this->addFlash('success','Serie added !');
            return $this->redirectToRoute('serie_details',['id'=>$serie->getId()]);
        }

        return $this->render('serie/create.html.twig',
        [
            'serieForm' => $serieForm->createView()
        ]);
    }

    /**
     * @Route("/demo", name="em_demo")
     */
    public function demo(EntityManagerInterface $entityManager): Response
    {
        // Création d'instance
        $serie = new Serie();

        // hydrate toutes les propriétés
        $serie->setName('pif');
        $serie->setBackdrop('dafsd');
        $serie->setPoster('dafsdf');
        $serie->setDateCreated(new \DateTime());
        $serie->setFirstAirDate(new \DateTime("-1 year"));
        $serie->setLastAirDate(new \DateTime("-6 month"));
        $serie->setGenres('drama');
        $serie->setOverview("bla bla b la bla");
        $serie->setPopularity(123.00);
        $serie->setVote(8.2);
        $serie->setStatus('Canceled');
        $serie->setTmdbId(8745612);

        dump($serie);

        $entityManager->persist($serie);
        $entityManager->flush();

        //$entityManager = $this->getDoctrine()->getManager();

        return $this->render('serie/create.html.twig');
    }

    /**
     *
     * @Route("/series/delete/{id}", name="serie_delete")
     */
    public function  delete(Serie $serie,EntityManagerInterface $entityManager)
    {
        $entityManager->remove($serie);
        $entityManager->flush();

        return $this->redirectToRoute('main_home');
    }

}
