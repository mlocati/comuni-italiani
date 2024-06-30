<?php

declare(strict_types=1);

namespace MLocati\ComuniItaliani\Build\Province;

use MLocati\ComuniItaliani\Province\Type as ProvinceType;

/**
 * @internal
 */
enum Type: int
{
    case Province = ProvinceType::PROVINCE;
    case AutonomousProvince = ProvinceType::AUTONOMOUS_PROVINCE;
    case MetropolitanCity = ProvinceType::METROPOLITAN_CITY;
    case FreeConsortiumOfMunicipalities = ProvinceType::FREE_CONSORTIUM_OF_MUNICIPALITIES;
    case NonAdministrativeUnit = ProvinceType::NON_ADMINISTRATIVE_UNIT;
}
