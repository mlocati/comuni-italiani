<?php

declare(strict_types=1);

namespace MLocati\ComuniItaliani\Region;

/**
 * All the possible types of a Region.
 *
 * @see \MLocati\ComuniItaliani\Region::getType()
 */
class Type
{
    /**
     * Region Type: Ordinary Status ("regione a statuto ordinario")
     */
    public const ORDINARY_STATUS = 1;

    /**
     * Region Type: Special Status ("regione a statuto speciale")
     */
    public const SPECIAL_STATUS = 2;
}
