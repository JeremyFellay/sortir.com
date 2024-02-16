<?php

namespace App\Controller;

use App\Form\ChangePasswordFormType;
use App\Form\EditProfilType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;

class ProfilController extends AbstractController
{
    #[Route('/profil/modifier', name: 'app_profilmodifier')]
    public function modifier(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Obtention de l'utilisateur actuellement connecté
        $user = $this->getUser();

        // Création du formulaire de modification de profil
        $form = $this->createForm(EditProfilType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Si le formulaire est soumis et valide, sauvegarde des modifications dans la base de données
            $entityManager->persist($user);
            $entityManager->flush();

            // Ajout d'un message flash pour informer l'utilisateur que ses modifications ont été enregistrées avec succès
            $this->addFlash(
                'success',
                'Vos modifications sont enregistrées'
            );

            // Redirection vers la page de profil de l'utilisateur
            return $this->redirectToRoute('app_monprofil');
        }

        // Affichage du formulaire de modification de profil
        return $this->render('profil/editerprofil.html.twig', [
            'EditProfilForm' => $form->createView(),
        ]);
    }

    #[Route('/profil', name: 'app_monprofil')]
    public function index(): Response
    {
        // Affichage de la page de profil de l'utilisateur actuellement connecté
        return $this->render('profil/monprofil.html.twig', [
            'controller_name' => 'CampusController',
        ]);
    }

    #[Route('/profil/modifiermdp', name: 'app_profilmodifiermdp')]
    public function changerMotDePasse(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $hasher): Response
    {
        // Obtention de l'utilisateur actuellement connecté
        $user = $this->getUser();

        // Création du formulaire de changement de mot de passe
        $form = $this->createForm(ChangePasswordFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Si le formulaire est soumis et valide, modification du mot de passe de l'utilisateur
            $motDePasse = $form['motDePasse']->getData();
            $user->setPassword(
                $hasher->hashPassword($user, $motDePasse)
            );

            // Sauvegarde des modifications dans la base de données
            $entityManager->persist($user);
            $entityManager->flush();

            // Ajout d'un message flash pour informer l'utilisateur que ses modifications ont été enregistrées avec succès
            $this->addFlash(
                'success',
                'Vos modifications sont enregistrées'
            );

            // Redirection vers la page de profil de l'utilisateur
            return $this->redirectToRoute('app_monprofil');
        }

        // Rafraîchissement des données de l'utilisateur depuis la base de données
        $entityManager->refresh($user);

        // Affichage du formulaire de changement de mot de passe
        return $this->render('profil/modifiermotdepasse.html.twig', [
            'ChangePasswordForm' => $form->createView(),
        ]);
    }

    #[Route('/profil/autreprofil/{id}', name: 'app_autreprofil', methods: ['GET'])]
    public function autreprofil(int $id, UserRepository $userRepository): Response
    {
        // Obtention des informations sur un autre utilisateur en fonction de son ID
        $user = $userRepository->find($id);

        // Affichage du profil de l'autre utilisateur
        return $this->render('profil/autreprofil.html.twig', [
            'user' => $user,
        ]);
    }
}





