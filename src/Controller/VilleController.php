<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleType;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ville')]
class VilleController extends AbstractController
{
    #[Route('/', name: 'app_ville_index', methods: ['GET'])]
    public function index(VilleRepository $villeRepository): Response
    {
        // Récupérer toutes les villes depuis le repository
        $villes = $villeRepository->findAll();

        // Afficher les villes dans le template associé
        return $this->render('ville/index.html.twig', [
            'villes' => $villes,
        ]);
    }

    #[Route('/new', name: 'app_ville_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Créer une nouvelle instance de Ville
        $ville = new Ville();

        // Créer le formulaire pour créer une nouvelle ville
        $form = $this->createForm(VilleType::class, $ville);
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide, enregistrer la nouvelle ville
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ville);
            $entityManager->flush();

            // Rediriger vers la page d'index des villes après l'enregistrement
            return $this->redirectToRoute('app_ville_index');
        }

        // Afficher le formulaire de création de ville
        return $this->render('ville/new.html.twig', [
            'ville' => $ville,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ville_show', methods: ['GET'])]
    public function show(Ville $ville): Response
    {
        // Afficher les détails d'une ville spécifique
        return $this->render('ville/show.html.twig', [
            'ville' => $ville,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_ville_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ville $ville, EntityManagerInterface $entityManager): Response
    {
        // Créer le formulaire de modification pour une ville existante
        $form = $this->createForm(VilleType::class, $ville);
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide, enregistrer les modifications
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            // Rediriger vers la page d'index des villes après la modification
            return $this->redirectToRoute('app_ville_index');
        }

        // Afficher le formulaire de modification de la ville
        return $this->render('ville/edit.html.twig', [
            'ville' => $ville,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ville_delete', methods: ['POST'])]
    public function delete(Request $request, Ville $ville, EntityManagerInterface $entityManager): Response
    {
        // Vérifier le token CSRF
        if ($this->isCsrfTokenValid('delete'.$ville->getId(), $request->request->get('_token'))) {
            // Supprimer la ville de la base de données
            $entityManager->remove($ville);
            $entityManager->flush();
        }

        // Rediriger vers la page d'index des villes après la suppression
        return $this->redirectToRoute('app_ville_index');
    }
}
