<?php

declare(strict_types=1);

namespace SefinSdk\Config;

enum EnvironmentType: int
{
    case PRODUCTION = 1;
    case HOMOLOGATION = 2;
}
