<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortiesController extends AbstractController
{
    #[Route('/sorties/sorties', name: 'app_sorties')]
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
    public function creation(): Response
    {
        return $this->render('sorties/creationSorties.html.twig', [
            'controller_name' => 'SortiesController',
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
