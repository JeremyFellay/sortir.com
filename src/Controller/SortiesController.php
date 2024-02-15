<?php

namespace App\Controller;

use App\Entity\FiltersSorties;
use App\Entity\Lieu;
use App\Entity\Sorties;
use App\Form\AnnulerSortieType;
use App\Form\FiltersSortiesType;
use App\Form\SortiesType;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\SortiesRepository;
use App\Repository\UserRepository;
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


        return $this->render('sorties/index.html.twig', [
            'sorties' => $sorties,
            'filtersForm' => $form->createView()
        ]);
    }

    #[Route('/new', name: 'app_sorties_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, EtatRepository $etatRepository): Response
    {
        $sortie = new Sorties();
        $user = $this->getUser();

        $sortie->setOrganisateur($user);
        $sortie->setCampus($user->getCampus());
        $sortie->setEtat($etatRepository->findOneBy(['libelle' => 'Créée']));

        $form = $this->createForm(SortiesType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sortie->setOrganisateur($user);
            $entityManager->persist($sortie);
            $entityManager->flush();

            if ($form->get('saveAndAdd')->isClicked()) {
                return $this->redirectToRoute('app_sorties_publier', ['id' => $sortie->getId()]);
            }

            return $this->redirectToRoute('app_sorties_index');
        }

        return $this->render('sorties/new.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
            'user' => $user
        ]);
    }

    #[Route('/{id}', name: 'app_sorties_show', methods: ['GET'])]
    public function show(int $id,UserRepository $userRepository,Sorties $sortie): Response
    {

        return $this->render('sorties/show.html.twig', [
            'sortie' => $sortie,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_sorties_edit', methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request, Sorties $sortie, EntityManagerInterface $entityManager, SortiesRepository $sortiesRepository): Response
    {
        $sortieModifier = $sortiesRepository->find($id);

        $form = $this->createForm(SortiesType::class, $sortieModifier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($sortieModifier);
            $entityManager->flush();
            $this->addFlash('success', 'Votre sortie est bien modifiée');
            if ($form->get('saveAndAdd')->isClicked()) {
                return $this->redirectToRoute('app_sorties_publier', ['id' => $sortieModifier->getId()]);
            }
            return $this->redirectToRoute('app_sorties_index');
        }
        {
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
        if ($this->isCsrfTokenValid('delete'.$sortie->getId(), $request->request->get('_token'))) {
            $entityManager->remove($sortie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_sorties_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/annuler/{id}', name: 'app_sorties_annuler', methods: ['GET', 'POST'])]
    public function annuler(EntityManagerInterface $entityManager,EtatRepository $etatRepository,Request $request, Sorties $sortie, int $id, SortiesRepository $sortiesRepository): Response
    {
        $sortieModifier = $sortiesRepository->find($id);
        $today = new \DateTime('now');
        $form = $this->createForm(AnnulerSortieType::class, $sortieModifier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($sortieModifier->getdateHeureDebut() > $today) {
                $sortie->setEtat($etatRepository->findOneBy(['libelle' => 'Annulée']));

                $entityManager->persist($sortie);
                $entityManager->flush();
                $this->addFlash('success', 'La sortie a été annulée.');
            }
            else
            {
                $this->addFlash('danger', 'Vous ne pouvez pas annuler cette sortie (elle est en cours).');
            }
            return $this->redirectToRoute('app_sorties_index');
        }
        return $this->render('sorties/annuler.html.twig', [
            'AnnulerForm' => $form->createView()
        ]);
    }

    #[Route('/inscription/{id}', name: 'app_sorties_inscription', methods: ['GET'])]
    public function inscription(int $id, SortiesRepository $sortiesRepository, EntityManagerInterface $entityManager ): Response
    {
        $sortie = $sortiesRepository -> find($id);
        $user = $this -> getUser();
        $today = new \DateTime('now');

        $inscriptionPossible = $sortie
            && $sortie->getEtat()->getLibelle()=='Ouverte'
            && $sortie->getDateLimiteInscription() >= $today
            && count ($sortie->getUsers()) < $sortie->getNbInscriptionsMax();
        if($inscriptionPossible)
        {
            $sortie->addUser($user);
            $entityManager->persist($sortie);
            $entityManager->flush();
           // $inscriptionEvent = new Sorties($sortie);
          //  $dispatcher->dispatch($inscriptionEvent, Sorties::INSCRIPTION);
            $this->addFlash('success', 'Votre inscription a été prise en compte.');
        }
        else
        {
            $this->addFlash('warning', 'Votre inscription n\'a pas pu être prise en compte (l\'organisateur.ice a annulé la sortie, le nombre maximum de participant.e.s est atteint ou la date limite d\'inscription est dépassée).');
        }
        return $this->redirectToRoute('app_sorties_index');

        //return $this->redirectToRoute('app_sorties_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/desinscription/{id}', name: 'app_sorties_desinscription', methods: ['GET'])]
    public function desinscription(int $id, SortiesRepository $sortiesRepository, EntityManagerInterface $entityManager ): Response
    {
        $sortie = $sortiesRepository -> find($id);
        $user = $this -> getUser();

        $sortie -> removeUser($user);
        $entityManager -> persist($sortie);
        $entityManager -> flush();


        return $this->redirectToRoute('app_sorties_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/sorties/publier/{id}', name: 'app_sorties_publier', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function publierSortie(int $id, SortiesRepository $sortieRepository, EtatRepository $etatRepository, EntityManagerInterface $entityManager): Response
    {
        $sortiePublier = $sortieRepository->find($id);
        $today = new \DateTime('today');
        if ($sortiePublier -> getDateLimiteInscription() >= $today && $sortiePublier->getDateHeureDebut() >= $today ){
            if ($sortiePublier->getEtat()->getLibelle() =='Créée'){
                $sortiePublier->setEtat($etatRepository->findOneBy(['libelle'=>'Ouverte']));
                $entityManager->persist($sortiePublier);
                $entityManager->flush();
                $this->addFlash('success', 'La sortie a bien été publiée');
            }
        }else{
            $this->addFlash('warning', "la date d'inscription ou la date de sortie est dépassée");
        }

        return $this->redirectToRoute('app_sorties_index');
    }


     // #[Route('/sorties/archives/{id}', name: 'app_sorties_archives', requirements: ['id' => '\d+'], methods: ['GET'])]
    //   public function archiver(SortiesRepository $sortiesRepository)
    //  {
    //     $archives = $sortiesRepository->findStillDisplayedSorties();

        // Vous pouvez ensuite transmettre $stillDisplayedSorties à votre vue ou effectuer d'autres opérations avec ces sorties.
    //    return $this->render('sorties/index.html.twig', [
    //       'stillDisplayedSorties' => $archives,
    //   ]);


    // }
}
