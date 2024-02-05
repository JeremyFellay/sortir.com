<?php

namespace App\Controller;

use App\Form\ConnexionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_connexion')]
    public function index1(Request $request): Response
    {
        $form = $this -> createForm(ConnexionType::class);
        $form -> handleRequest($request);
        return $this->render('main/index.html.twig', [
            'connexionForm' => $form -> createView(),
        ]);
    }
}
