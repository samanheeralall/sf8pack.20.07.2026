<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Loan;
use App\Loan\LoanManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/loans')]  // ← préfixe commun à toutes les routes
class LoanController extends AbstractController
{
    public function __construct(private readonly LoanManager $loanManager) {}

    #[Route('', name: 'app_loan_index')]                              // → /loans        (loan_index)
    public function index(): Response
    {
        $user = $this->getUser();
        return $this->render('loan/index.html.twig', [
            'loans' => $user->getLoans(),
        ]);
    }

    #[Route('/new/{id}', name: 'app_loan_new', methods: ['POST'])]    // → /loans/new/1  (loan_new)
    public function new(Book $book): Response
    {
        try {
            $this->loanManager->createLoan($this->getUser(), $book);
            $this->addFlash('success', 'Prêt créé avec succès !');
        } catch (\RuntimeException $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('app_loan_index');
    }

    #[Route('/{id}/return', name: 'app_loan_return', methods: ['POST'])] // → /loans/1/return (loan_return)
    public function return(Loan $loan): Response
    {
        $this->loanManager->returnLoan($loan);
        $this->addFlash('success', 'Livre retourné !');

        return $this->redirectToRoute('app_loan_index');
    }
}
