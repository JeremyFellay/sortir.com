<?php

namespace App\Controller;

use App\Entity\FiltersSorties;
use App\Entity\Lieu;
use App\Entity\Sorties;
use App\Form\FiltersSortiesType;
use App\Form\SortiesType;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\SortiesRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/sorties')]
class SortiesController extends AbstractController
{
    #[Route('/', name: 'app_sorties_index', methods: ['GET', 'POST'])]
    public function index(SortiesRepository $sortiesRepository, Request $request): Response
    {
        $sorties = $sortiesRepository->findAll();

        // Classe D.T.O.
        $oFilters = new FiltersSorties();

        // On associe la classe de D.T.O. à son formulaire spécifique, ici FiltersSortiesFormType::class
        $form = $this->createForm(FiltersSortiesType::class, $oFilters);
        $form->handleRequest($request);

        // On récupère l'utilisateur connecté
        $oUser = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            $sorties = $sortiesRepository->findFilteredSorties($oFilters, $oUser);
        }


        return $this->render('sorties/index.html.twig',[
            'sorties' => $sorties,
            'filtersForm' => $form->createView()
        ]);
    }

    #[Route('/new', name: 'app_sorties_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, EtatRepository $etatRepository): Response
    {
       $sortie = new Sorties();
       $user = $this -> getUser();

       $sortie->setOrganisateur($user);
       $sortie -> setCampus($user -> getCampus());

        $form = $this->createForm(SortiesType::class, $sortie);
        $form-> handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sortie -> setOrganisateur($user);
            $entityManager->persist($sortie);
            $entityManager->flush();

            return $this->redirectToRoute('app_sorties_index');
        }

        return $this->render('sorties/new.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
            'user' => $user
        ]);
    }

    #[Route('/{id}', name: 'app_sorties_show', methods: ['GET'])]
    public function show(Sorties $sortie): Response
    {
        return $this->render('sorties/show.html.twig', [
            'sortie' => $sortie,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_sorties_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Sorties $sortie, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SortiesType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_sorties_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sorties/edit.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sorties_delete', methods: ['POST'])]
    public function delete(Request $request, Sorties $sortie, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sortie->getId(), $request->request->get('_token'))) {
            $entityManager->remove($sortie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_sorties_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/inscription/{id}', name: 'app_sorties_inscription', methods: ['GET'])]
    public function inscription(int $id, SortiesRepository $sortiesRepository, EntityManagerInterface $entityManager ): Response
    {
        $sortie = $sortiesRepository -> find($id);
        $user = $this -> getUser();

        $sortie -> addUser($user);
        $entityManager -> persist($sortie);
        $entityManager -> flush();


        return $this->redirectToRoute('app_sorties_index', [], Response::HTTP_SEE_OTHER);
    }

}
