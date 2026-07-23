<?php

namespace App\Search;

enum BookSortColumn: string
{
    case Title           = 'title';
    case Author          = 'author';
    case PublicationDate = 'publicationDate';
}
