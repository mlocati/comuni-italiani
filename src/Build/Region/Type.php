<?php

declare(strict_types=1);

namespace MLocati\ComuniItaliani\Build\Region;

use MLocati\ComuniItaliani\Region\Type as RegionType;

/**
 * @internal
 */
enum Type: int
{
    case OrdinaryStatus = RegionType::ORDINARY_STATUS;
    case SpecialStatus = RegionType::SPECIAL_STATUS;
}
