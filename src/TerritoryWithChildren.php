<?php

declare(strict_types=1);

namespace MLocati\ComuniItaliani;

interface TerritoryWithChildren extends Territory
{
    /**
     * @return \MLocati\ComuniItaliani\Territory[]
     */
    public function getChildren(): array;
}
