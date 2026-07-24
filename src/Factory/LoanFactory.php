<?php

namespace App\Factory;

use App\Entity\Loan;
use App\Loan\LoanStatus;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Loan>
 */
final class LoanFactory extends PersistentObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    #[\Override]
    public static function class(): string
    {
        return Loan::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'book' => BookFactory::new(),
            'dueDate' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'loanDate' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'status' => self::faker()->randomElement(LoanStatus::cases()),
            'user' => UserFactory::new(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    #[\Override]
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Loan $loan): void {})
            ;
    }
}
