<?php

declare(strict_types=1);

namespace MLocati\ComuniItaliani;

abstract class TerritoryWithChildren extends Territory
{
    /**
     * @return \MLocati\ComuniItaliani\Territory[]
     */
    abstract public function getChildren(): array;
}
