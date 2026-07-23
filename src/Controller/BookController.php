<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookSearchType;
use App\Repository\BookRepository;
use App\Repository\GenreRepository;
use App\Search\BookSearchCriteria;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/books', name: 'app_book_')]
final class BookController extends AbstractController
{
    private const int PER_PAGE = 10;
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(
        BookRepository $bookRepository,
        GenreRepository $genreRepository,
        #[MapQueryString] BookSearchCriteria $criteria = new BookSearchCriteria(),
        #[MapQueryParameter] int $page = 1,
    ): Response
    {
        $page      = max(1, $page);
        $paginator = $bookRepository->search($criteria, $page, self::PER_PAGE);
        $total     = count($paginator);

        return $this->render('book/index.html.twig', [
            'books'      => $paginator,
            'genres'     => $genreRepository->findAll(),
            'criteria'   => $criteria,
            'page'       => $page,
            'totalPages' => (int) ceil($total / self::PER_PAGE),
            'total'      => $total,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Book $book): Response
    {
        return $this->render('book/show.html.twig', [
            'book' => $book,
        ]);
    }
}
