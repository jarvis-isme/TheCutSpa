<?php

namespace App\Models\Enums;

enum OtpTypeEnum : int {
    case REGISTER = 0;
    case RESET_PASSWORD = 1;
}
