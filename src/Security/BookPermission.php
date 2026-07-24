<?php

namespace App\Security;

enum BookPermission
{
    public const string EDIT_DETAILS = 'book.edit_details';
    public const string CHANGE_AVAILABILITY = 'book.change_availability';
}
