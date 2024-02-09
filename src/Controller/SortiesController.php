<?php

namespace App\Controller;

use App\Entity\Sorties;
use App\Entity\User;
use App\Form\CreationSortieType;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortiesController extends AbstractController
{
    #[Route('/sorties', name: 'app_sorties')]
    public function index(): Response
    {
        return $this->render('sorties/sorties.html.twig', [
            'controller_name' => 'SortiesController',
        ]);
    }

    #[Route('/sorties/afficher', name: 'app_afficherSorties')]
    public function afficher(): Response
    {
        return $this->render('sorties/afficherSorties.html.twig', [
            'controller_name' => 'SortiesController',
        ]);
    }

    #[Route('/sorties/annuler', name: 'app_annulerSorties')]
    public function annuler(): Response
    {
        return $this->render('sorties/annulerSorties.html.twig', [
            'controller_name' => 'SortiesController',
        ]);
    }

    #[Route('/sorties/creation', name: 'app_creationSorties')]
    public function creation(Request $request, EntityManagerInterface $entityManager): Response
    {
        $sorties = new Sorties();
        $form = $this->createForm(CreationSortieType::class, $sorties);
        $form->handleRequest($request);
        $form -> getErrors();

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($sorties);
            $entityManager->flush();
            // do anything else you need here, like send an email

            $this->addFlash(
                'success',
                "La sortie a bien été crée"
            );

            return $this->redirectToRoute('app_sorties');
        }
        return $this->render('sorties/creationSorties.html.twig', [
            'CreationSortieForm' => $form->createView(),
        ]);
    }
    #[Route('/sorties/modifier', name: 'app_modifierSorties')]
    public function modifier(): Response
    {
        return $this->render('sorties/modifierSorties.html.twig', [
            'controller_name' => 'SortiesController',
        ]);
    }
}
