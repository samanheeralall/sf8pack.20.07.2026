<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BookController extends AbstractController
{
    #[Route('/books/{slug}', name: 'app_book_show')]
    public function show(string $slug): Response
    {
        return new Response("Showing: $slug");
    }

    #[Route('/books/new', name: 'app_book_new', priority: 1)]
    public function new(): Response
    {
        return new Response('New book form');
    }
}
