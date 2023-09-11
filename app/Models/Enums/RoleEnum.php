<?php

namespace App\Models\Enums;

enum RoleEnum : int {
    case STAFF = 4;
    case ADMIN = 3;
    case MANAGER = 2;
    case CUSTOMER = 1;
}
