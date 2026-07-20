<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('main/index.html.twig');
    }

    #[Route('/about', name: 'app_main_about', methods: ['GET'])]
    public function about(): Response
    {
        return $this->render('main/about.html.twig');
    }

    #[Route('/contact', name: 'app_main_contact', methods: ['GET'])]
    public function contact(): Response
    {
        return $this->render('main/contact.html.twig');
    }
}
