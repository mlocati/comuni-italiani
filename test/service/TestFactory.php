<?php

declare(strict_types=1);

namespace MLocati\ComuniItaliani\Test\Service;

use MLocati\ComuniItaliani\Factory;

class TestFactory extends Factory
{
    public bool $loadUnminified = false;

    /**
     * {@inheritdoc}
     *
     * @see \MLocati\ComuniItaliani\Factory::getTerritoryData()
     */
    protected function getTerritoryData(): array
    {
        if ($this->loadUnminified) {
            return require __DIR__ . '/../../src/data/geographical-subdivisions.unminified.php';
        }

        return parent::getTerritoryData();
    }
}
