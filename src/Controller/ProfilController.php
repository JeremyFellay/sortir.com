<?php

namespace App\Controller;

use App\Form\ChangePasswordFormType;
use App\Form\EditProfilType;
use App\Form\UpdatePasswordType;
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
        $user = $this->getUser();
        $form = $this->createForm(EditProfilType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Vos modifications sont enregistrées'
            );

            return $this->redirectToRoute('app_monprofil');
        }

        //$entityManager->persist($user);
        //$entityManager->flush();

        return $this->render('profil/editerprofil.html.twig', [
            'EditProfilForm' => $form->createView(),
        ]);
    }

    #[Route('/monprofil', name: 'app_monprofil')]
    public function index(): Response
    {
        return $this->render('profil/monprofil.html.twig', [
            'controller_name' => 'CampusController',
        ]);
    }

    #[Route('/profil/modifiermdp', name: 'app_profilmodifiermdp')]
    public function changerMotDePasse(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $hasher): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordFormType::class, $user);
        $form->handleRequest($request);

            // TO DO : RAJOUTER VÉRIF MOT DE PASSE ACTUEL

            if ($form->isSubmitted() && $form->isValid()) {

                $motDePasse = $form['motDePasse']->getData();
                //S l'utilsateur saisi un nouveau de mot de passe, on le vérifie et on modifie l'attribut mot de passe s'il est valide.
                if (!is_null($motDePasse) && !empty(trim($motDePasse))) {
                    $user->setPassword(
                        $hasher->hashPassword($user, $motDePasse)
                    );
                }
                if (!is_null($motDePasse) && empty($motDePasse)) {
                    $this->addFlash('warning', 'Le mot de passe ne peut être une chaîne vide !');
                    return $this->redirectToRoute('app_profilmodifiermdp');
                }
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash(
                    'success',
                    'Vos modifications sont enregistrées'
                );

                return $this->redirectToRoute('app_monprofil');
            }

            $entityManager->refresh($user);

            return $this->render('profil/modifiermotdepasse.html.twig', [
                'ChangePasswordForm' => $form->createView(),
            ]);
        }
    #[Route('/autreprofil/{id}', name: 'app_autreprofil', methods: ['GET'])]
    public function autreprofil(int $id, UserRepository $userRepository): Response
    {
        $user=$userRepository -> find($id);

        return $this->render('profil/autreprofil.html.twig', [
            'user' => $user,
        ]);
    }
    }





