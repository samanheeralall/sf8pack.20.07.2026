<?php

namespace App\Story;

use App\Factory\AuthorFactory;
use App\Factory\BookFactory;
use App\Factory\GenreFactory;
use App\Factory\UserFactory;
use Zenstruck\Foundry\Attribute\AsFixture;
use Zenstruck\Foundry\Story;

#[AsFixture('catalog')]
final class LibraryCatalogStory extends Story
{
    public function build(): void
    {
        UserFactory::createOne(['email' => 'admin@library.local', 'roles' => ['ROLE_ADMIN']]);
        UserFactory::createOne(['email' => 'reader@library.local']);

        $books = require dirname(__DIR__, 2) . '/fixtures/book_fixtures.php';

        BookFactory::createMany(\count($books), static function (int $i) use ($books) {
            $book   = $books[$i - 1];
            $genres = $book['genres'];
            $author = $book['author'];

            unset($book['author'], $book['genres']);

            return [
                ...$book,
                'authors' => [AuthorFactory::findOrCreate(['name' => $author])],
                'genres' => array_map(
                    static fn (string $name) => GenreFactory::findOrCreate(['name' => $name]),
                    $genres,
                ),
            ];
        });
    }
}
