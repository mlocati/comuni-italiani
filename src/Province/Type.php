<?php

declare(strict_types=1);

namespace MLocati\ComuniItaliani\Province;

/**
 * All the possible types of a Province/UTS.
 *
 * @see \MLocati\ComuniItaliani\Province::getType()
 */
class Type
{
    /**
     * Province/UTS Type: Province ("provincia")
     */
    public const PROVINCE = 1;

    /**
     * Province/UTS Type: Autonomous Province ("provincia autonoma")
     */
    public const AUTONOMOUS_PROVINCE = 2;

    /**
     * Province/UTS Type: Metropolitan City ("città metropolitana")
     */
    public const METROPOLITAN_CITY = 3;

    /**
     * Province/UTS Type: Free Consortium of Municipalities ("libero consorzio di comuni")
     */
    public const FREE_CONSORTIUM_OF_MUNICIPALITIES = 4;

    /**
     * Province/UTS Type: Non-Administrative Unit ("unità non amministrativa") - former Provinces of Friuli-Venezia Giulia
     */
    public const NON_ADMINISTRATIVE_UNIT = 5;
}
