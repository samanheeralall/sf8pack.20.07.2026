<?php

namespace App\Story;

use App\Factory\BookFactory;
use App\Factory\GenreFactory;
use Zenstruck\Foundry\Attribute\AsFixture;
use Zenstruck\Foundry\Story;

#[AsFixture('catalog')]
final class LibraryCatalogStory extends Story
{
    public function build(): void
    {
        $books = require dirname(__DIR__, 2) . '/fixtures/book_fixtures.php';

        BookFactory::createMany(\count($books), static function (int $i) use ($books) {
            $book   = $books[$i - 1];
            $genres = $book['genres'];

            unset($book['author'], $book['genres']);

            return [
                ...$book,
                'genres' => array_map(
                    static fn (string $name) => GenreFactory::findOrCreate(['name' => $name]),
                    $genres,
                ),
            ];
        });
    }
}
