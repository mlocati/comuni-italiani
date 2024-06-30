<?php

declare(strict_types=1);

namespace MLocati\ComuniItaliani\Build;

use Collator;
use RuntimeException;
use stdClass;

/**
 * @internal
 */
final class GeographicalSubdivision
{
    public readonly int $id;

    public readonly string $name;

    public readonly string $nuts1;

    /**
     * @var \MLocati\ComuniItaliani\Build\Region[]
     */
    public array $regions = [];

    public function __construct(stdClass $data)
    {
        $this->id = $data->COD_RIP;
        $this->name = $data->DEN_RIP;
        $this->nuts1 = $data->COD_NUTS1_2024;
    }

    public function checkRegionData(stdClass $data): void
    {
        if ($this->id !== $data->COD_RIP
            || $this->name !== $data->DEN_RIP
        ) {
            throw new RuntimeException('Incompatible Region data');
        }
    }

    public function checkProvinceData(stdClass $data): void
    {
        if ($this->id !== $data->COD_RIP
            || $this->name !== $data->DEN_RIP
        ) {
            throw new RuntimeException('Incompatible Province data');
        }
    }

    public function checkMunicipalityData(stdClass $data): void
    {
        if ($this->id !== $data->COD_RIP
            || $this->name !== $data->DEN_RIP
        ) {
            throw new RuntimeException('Incompatible Municipality data');
        }
    }

    public function serialize(Collator $collator): array
    {
        $result = [
            'id' => $this->id,
            'name' => $this->name,
            'nuts1' => $this->nuts1,
        ];
        $children = array_map(
            static fn(Region $region): array => $region->serialize($collator),
            $this->regions
        );
        usort($children, static fn(array $a, array $b) => $collator->compare($a['name'], $b['name']));
        $result['children'] = $children;

        return $result;
    }
}
