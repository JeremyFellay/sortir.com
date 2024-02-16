<?php

namespace App\Controller;

use App\Entity\FiltersSorties;
use App\Entity\Sorties;
use App\Form\AnnulerSortieType;
use App\Form\FiltersSortiesType;
use App\Form\SortiesType;
use App\Repository\EtatRepository;
use App\Repository\SortiesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sorties')]
class SortiesController extends AbstractController
{
    #[Route('/', name: 'app_sorties_index', methods: ['GET', 'POST'])]
    public function index(SortiesRepository $sortiesRepository, Request $request): Response
    {
        // Récupération de toutes les sorties
        $sorties = $sortiesRepository->findAll();

        // Création d'un objet de filtres pour les sorties
        $oFilters = new FiltersSorties();

        // Création du formulaire associé aux filtres des sorties
        $form = $this->createForm(FiltersSortiesType::class, $oFilters);
        $form->handleRequest($request);

        // Récupération de l'utilisateur connecté
        $oUser = $this->getUser();

        // Si le formulaire est soumis et valide, on filtre les sorties
        if ($form->isSubmitted() && $form->isValid()) {
            $sorties = $sortiesRepository->findFilteredSorties($oFilters, $oUser);
        }

        return $this->render('sorties/index.html.twig', [
            'sorties' => $sorties,
            'filtersForm' => $form->createView()
        ]);
    }

    #[Route('/new', name: 'app_sorties_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, EtatRepository $etatRepository): Response
    {
        // Création d'une nouvelle sortie
        $sortie = new Sorties();
        // Récupération de l'utilisateur
        $user = $this->getUser();

        // Attribution de l'utilisateur actuel comme organisateur de la sortie
        $sortie->setOrganisateur($user);
        // Attribution du campus de l'utilisateur actuel à la sortie
        $sortie->setCampus($user->getCampus());
        // Attribution de l'état 'Créée' à la sortie en recherchant l'état correspondant dans la BDD
        $sortie->setEtat($etatRepository->findOneBy(['libelle' => 'Créée']));

        // Création du formulaire de création de sortie
        $form = $this->createForm(SortiesType::class, $sortie);
        $form->handleRequest($request);

        // Vérification si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Attribution de l'utilisateur actuel comme organisateur de la sortie
            $sortie->setOrganisateur($user);
            // Persiste la sortie dans la base de données
            $entityManager->persist($sortie);
            // Applique les changements enregistrés dans la base de données
            $entityManager->flush();

            // Redirection vers la liste des sorties ou vers la page de publication de la sortie
            if ($form->get('saveAndAdd')->isClicked()) {
                return $this->redirectToRoute('app_sorties_publier', ['id' => $sortie->getId()]);
            }

            return $this->redirectToRoute('app_sorties_index');
        }
        // Passage des variables à la vue
        return $this->render('sorties/new.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
            'user' => $user
        ]);
    }

    #[Route('/{id}', name: 'app_sorties_show', methods: ['GET'])]
    public function show(int $id, Sorties $sortie): Response
    {
        return $this->render('sorties/show.html.twig', [
            'sortie' => $sortie,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_sorties_edit', methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request, Sorties $sortie, EntityManagerInterface $entityManager, SortiesRepository $sortiesRepository): Response
    {
        // Récupération de la sortie à modifier
        $sortieModifier = $sortiesRepository->find($id);

        // Création du formulaire de modification de sortie
        $form = $this->createForm(SortiesType::class, $sortieModifier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrement des modifications
            $entityManager->persist($sortieModifier);
            $entityManager->flush();
            $this->addFlash('success', 'Votre sortie est bien modifiée');

            // Redirection vers la liste des sorties ou vers la page de publication de la sortie
            if ($form->get('saveAndAdd')->isClicked()) {
                return $this->redirectToRoute('app_sorties_publier', ['id' => $sortieModifier->getId()]);
            }
            return $this->redirectToRoute('app_sorties_index');
        } else {
            $this->addFlash('danger', 'La modification n\'est pas permise pour cette sortie.');
        }

        return $this->render('sorties/edit.html.twig', [
            'sortie' => $sortie,
            'form' => $form
        ]);
    }

    #[Route('/{id}', name: 'app_sorties_delete', methods: ['POST'])]
    public function delete(Request $request, Sorties $sortie, EntityManagerInterface $entityManager): Response
    {
        // Suppression de la sortie
        if ($this->isCsrfTokenValid('delete'.$sortie->getId(), $request->request->get('_token'))) {
            $entityManager->remove($sortie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_sorties_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/annuler/{id}', name: 'app_sorties_annuler', methods: ['GET', 'POST'])]
    public function annuler(EntityManagerInterface $entityManager, EtatRepository $etatRepository, Request $request, Sorties $sortie, int $id, SortiesRepository $sortiesRepository): Response
    {
        // Récupération de la sortie à annuler
        $sortieModifier = $sortiesRepository->find($id);
        $today = new \DateTime('now');

        // Création du formulaire d'annulation de sortie
        $form = $this->createForm(AnnulerSortieType::class, $sortieModifier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($sortieModifier->getdateHeureDebut() > $today) {
                // Annulation de la sortie si elle n'est pas encore commencée
                $sortie->setEtat($etatRepository->findOneBy(['libelle' => 'Annulée']));

                $entityManager->persist($sortie);
                $entityManager->flush();
                $this->addFlash('success', 'La sortie a été annulée.');
            } else {
                $this->addFlash('danger', 'Vous ne pouvez pas annuler cette sortie (elle est en cours).');
            }
            return $this->redirectToRoute('app_sorties_index');
        }

        return $this->render('sorties/annuler.html.twig', [
            'AnnulerForm' => $form->createView()
        ]);
    }

    #[Route('/inscription/{id}', name: 'app_sorties_inscription', methods: ['GET'])]
    public function inscription(int $id, SortiesRepository $sortiesRepository, EntityManagerInterface $entityManager): Response
    {
        // Récupération de la sortie pour inscription
        $sortie = $sortiesRepository->find($id);
        $user = $this->getUser();
        $today = new \DateTime('now');

        // Vérification de l'éligibilité à l'inscription
        $inscriptionPossible = $sortie
            && $sortie->getEtat()->getLibelle() == 'Ouverte'
            && $sortie->getDateLimiteInscription() >= $today
            && count($sortie->getUsers()) < $sortie->getNbInscriptionsMax();

        // Inscription à la sortie si possible
        if ($inscriptionPossible) {
            $sortie->addUser($user);
            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash('success', 'Votre inscription a été prise en compte.');
        } else {
            $this->addFlash('warning', 'Votre inscription n\'a pas pu être prise en compte (l\'organisateur.ice a annulé la sortie, le nombre maximum de participant.e.s est atteint ou la date limite d\'inscription est dépassée).');
        }

        return $this->redirectToRoute('app_sorties_index');
    }

    #[Route('/desinscription/{id}', name: 'app_sorties_desinscription', methods: ['GET'])]
    public function desinscription(int $id, SortiesRepository $sortiesRepository, EntityManagerInterface $entityManager): Response
    {
        // Récupération de la sortie pour désinscription
        $sortie = $sortiesRepository->find($id);
        $user = $this->getUser();

        // Désinscription de la sortie
        $sortie->removeUser($user);
        $entityManager->persist($sortie);
        $entityManager->flush();

        return $this->redirectToRoute('app_sorties_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/sorties/publier/{id}', name: 'app_sorties_publier', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function publierSortie(int $id, SortiesRepository $sortieRepository, EtatRepository $etatRepository, EntityManagerInterface $entityManager): Response
    {
        // Récupération de la sortie à publier
        $sortiePublier = $sortieRepository->find($id);
        $today = new \DateTime('today');

        if ($sortiePublier->getDateLimiteInscription() >= $today && $sortiePublier->getDateHeureDebut() >= $today) {
            // Vérification et mise à jour de l'état de la sortie
            if ($sortiePublier->getEtat()->getLibelle() == 'Créée') {
                $sortiePublier->setEtat($etatRepository->findOneBy(['libelle' => 'Ouverte']));
                $entityManager->persist($sortiePublier);
                $entityManager->flush();
                $this->addFlash('success', 'La sortie a bien été publiée');
            }
        } else {
            $this->addFlash('warning', "la date d'inscription ou la date de sortie est dépassée");
        }

        return $this->redirectToRoute('app_sorties_index');
    }
}