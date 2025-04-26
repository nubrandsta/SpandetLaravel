<?php

namespace App\Enums;

enum UserGroup: string
{
    case ADMIN = 'admin';
    case USER = 'user';
    case AUDITOR = 'auditor';
}