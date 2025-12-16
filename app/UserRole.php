<?php

namespace App;

enum UserRole: string
{
    case Admin = 'administrator';
    case Manager = 'manager';
    case User = 'user';
}
