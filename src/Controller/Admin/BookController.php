<?php

namespace App\Controller\Admin;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use App\Security\BookPermission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_LIBRARIAN')]
class BookController extends AbstractController
{
    #[Route('/admin/books', name: 'app_admin_books')]
    public function index(BookRepository $bookRepository): Response
    {
        return $this->render("admin/book/index.html.twig", [
            'books' => $bookRepository->findAll(),
        ]);
    }
    #[Route('/admin/books/new', name: 'app_admin_book_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_LIBRARIAN')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book, [
            'can_change_availability' => $this->isGranted('book.change_availability', $book)
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($book);
            $em->flush();

            // PATTERN PRG
            $this->addFlash('success', 'Book created successfully!');
            return $this->redirectToRoute('app_book_index');
        }

        return $this->render('admin/book/save.html.twig', [
            'form' => $form,
            'book' => $book,
        ]);
    }

    #[Route('/admin/books/{id}/edit', name: 'app_admin_book_edit', methods: ['GET', 'POST'])]
    #[IsGranted(BookPermission::EDIT_DETAILS, subject: 'book')]
    public function edit(Request $request,Book $book,EntityManagerInterface $em): Response
    {
        /*        $this->denyAccessUnlessGranted(
            BookPermission::EDIT_DETAILS,
            $book
        );*/

        $form = $this->createForm(BookType::class, $book, [
            'can_change_availability' => $this->isGranted('book.change_availability', $book)
        ]);

        $dateField = $form->get('publicationDate');

        dump('1 — Après createForm(), avant handleRequest()', [
            'valeur dans Book' => $book->getPublicationDate(),
            'model data' => $dateField->getData(),
            'normalized data' => $dateField->getNormData(),
            'view data' => $dateField->getViewData(),
        ]);

        if ($request->isMethod('POST')) {
            $rawData = $request->request->all($form->getName());

            dump('2 — Donnée HTTP brute', [
                'publicationDate' => $rawData['publicationDate'] ?? null,
            ]);
        }


        $form->handleRequest($request);

/*        if ($form->isSubmitted()) {
            $dateField = $form->get('publicationDate');

            dd('3 — Après handleRequest()', [
                'view data' => $dateField->getViewData(),
                'normalized data' => $dateField->getNormData(),
                'model data' => $dateField->getData(),

                'valeur placée dans Book' => $book->getPublicationDate(),

                'transformation réussie' => $dateField->isSynchronized(),
                'formulaire valide' => $form->isValid(),
            ]);
        }*/

        if ($form->isSubmitted() && $form->isValid()) {
            $book->setAddedBy($this->getUser());
            $em->flush();

            $this->addFlash('success', 'Book created successfully!');
            return $this->redirectToRoute('app_book_index');
        }

        return $this->render('admin/book/save.html.twig', [
            'form' => $form,
            'book' => $book,
        ]);
    }
}
