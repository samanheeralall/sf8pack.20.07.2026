<?php

namespace App\Loan;

use App\Entity\Book;
use App\Entity\Loan;
use App\Entity\User;
use App\Event\LoanCreatedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class LoanManager
{
    private const DEFAULT_DURATION_DAYS = 14;
    public function __construct(          // 1. Dependencies
        private EntityManagerInterface $em,
        private EventDispatcherInterface $eventDispatcher
    ) {}

    public function createLoan(User $user, Book $book): Loan  // 2. Domain verb
    {
        // 3. Business rule guard
        if (!$book->isAvailable()) {
            throw new \RuntimeException('Book not available');
        }

        // Créer le prêt
        $loan = new Loan();
        $loan->setUser($user);
        $loan->setBook($book);
        $loan->setLoanDate(new \DateTimeImmutable());
        $loan->setDueDate(new \DateTimeImmutable('+14 days'));
        //->setDueDate(new \DateTimeImmutable()->modify('+'.self::DEFAULT_DURATION_DAYS.' days'))
        $loan->setStatus(LoanStatus::Active);

        // Flip book availability
        $book->setAvailable(false);

        // 4. Side effects
        $this->em->persist($loan);
        $this->em->flush();

        $this->eventDispatcher->dispatch(new LoanCreatedEvent($loan));

        return $loan;
    }

    public function returnLoan(Loan $loan): void  // 2. Domain verb
    {
        // 3. Business rule guard — (optionnel)

        // Mettre à jour le prêt
        $loan->setReturnDate(new \DateTimeImmutable());
        $loan->setStatus(LoanStatus::Returned);

        // Flip book availability back
        $loan->getBook()->setAvailable(true);

        // 4. Side effects
        $this->em->flush();
    }
}
