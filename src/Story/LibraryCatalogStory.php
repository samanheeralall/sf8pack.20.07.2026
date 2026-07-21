<?php

namespace App\Story;

use App\Factory\BookFactory;
use Zenstruck\Foundry\Attribute\AsFixture;
use Zenstruck\Foundry\Story;

#[AsFixture('catalog')]
final class LibraryCatalogStory extends Story
{
    public function build(): void
    {
        $books = require dirname(__DIR__, 2) . '/fixtures/book_fixtures.php';

        BookFactory::createMany(\count($books), static function (int $i) use ($books) {
            // Day 2: strip relation data — the entity has no authors/genres yet
            unset($books[$i - 1]['author'], $books[$i - 1]['genres']);

            return $books[$i - 1];
        });
    }
}
