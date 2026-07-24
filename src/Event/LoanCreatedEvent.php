<?php

namespace App\Event;

use App\Entity\Loan;
use Symfony\Contracts\EventDispatcher\Event;

class LoanCreatedEvent extends Event
{
    public function __construct(private Loan $loan) {}

    public function getLoan(): Loan { return $this->loan; }

}
