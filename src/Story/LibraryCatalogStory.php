<?php

namespace App\Story;

use App\Factory\AuthorFactory;
use App\Factory\BookFactory;
use App\Factory\GenreFactory;
use App\Factory\LoanFactory;
use App\Factory\UserFactory;
use App\Loan\LoanStatus;
use Zenstruck\Foundry\Attribute\AsFixture;
use Zenstruck\Foundry\Story;

#[AsFixture(name: 'catalog')]
final class LibraryCatalogStory extends Story
{
    public function build(): void
    {
        $admin     = UserFactory::createOne(['email' => 'admin@library.local',     'roles' => ['ROLE_ADMIN']]);
        $manager   = UserFactory::createOne(['email' => 'manager@library.local',   'roles' => ['ROLE_MANAGER']]);
        $librarian = UserFactory::createOne(['email' => 'librarian@library.local', 'roles' => ['ROLE_LIBRARIAN']]);
        $webmaster = UserFactory::createOne(['email' => 'webmaster@library.local', 'roles' => ['ROLE_WEBMASTER']]);
        $reader    = UserFactory::createOne(['email' => 'reader@library.local']);

        $books = require dirname(__DIR__,2) . '/fixtures/book_fixtures.php';

        $contributors = [$librarian, $librarian, $reader];

        BookFactory::createMany(\count($books), static function (int $i) use ($books, $contributors) {
            $author = $books[$i - 1]['author'];
            $genres = $books[$i - 1]['genres'];
            unset($books[$i - 1]['author'], $books[$i - 1]['genres']);

            return [
                ...$books[$i - 1],
                'authors' => [AuthorFactory::findOrCreate(['name' => $author])],
                'genres' => array_map(
                    static fn (string $name) => GenreFactory::findOrCreate(['name' => $name]),
                    $genres,
                ),
                'addedBy'  => $contributors[$i % 3],
            ];
        });


        $activeBook  = BookFactory::random(['available' => false]); // librarian's active loan
        $overdueBook = BookFactory::random(['available' => false]); // reader's overdue loan

        LoanFactory::createOne([
            'user' => $librarian, 'book' => $activeBook,
            'loanDate' => new \DateTimeImmutable('-3 days'),
            'dueDate'  => new \DateTimeImmutable('+11 days'),
            'status'   => LoanStatus::Active,
        ]);

        LoanFactory::createOne([
            'user' => $reader, 'book' => $overdueBook,
            'loanDate' => new \DateTimeImmutable('-30 days'),
            'dueDate'  => new \DateTimeImmutable('-16 days'),
            'status'   => LoanStatus::Overdue,
        ]);
    }
}
