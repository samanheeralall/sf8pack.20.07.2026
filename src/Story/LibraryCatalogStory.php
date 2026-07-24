<?php

namespace App\Story;

use App\Factory\AuthorFactory;
use App\Factory\BookFactory;
use App\Factory\GenreFactory;
use App\Factory\UserFactory;
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
    }
}
