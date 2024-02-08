<?php

namespace App\Controller;

use App\Form\EditProfilType;
use App\Form\UpdatePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{

    #[Route('/profil/modifier', name: 'app_profilmodifier')]
    public function modifier(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this -> getUser();
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
}
