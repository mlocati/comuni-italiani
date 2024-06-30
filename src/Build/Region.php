<?php

declare(strict_types=1);

namespace MLocati\ComuniItaliani\Build;

use Collator;
use RuntimeException;
use stdClass;

/**
 * @internal
 */
final class Region
{
    public readonly string $id;

    public readonly string $name;

    public readonly Region\Type $type;

    public readonly string $fiscalCode;

    public readonly string $nuts2;

    /**
     * @var \MLocati\ComuniItaliani\Build\Province[]
     */
    public array $provinces = [];

    public function __construct(
        public readonly GeographicalSubdivision $geographicalSubdivision,
        stdClass $data,
    ) {
        $this->id = str_pad((string) $data->COD_REG, 2, '0', STR_PAD_LEFT);
        $this->name = $data->DEN_REG;
        $this->type = Region\Type::from($data->TIPO_REG);
        $this->fiscalCode = str_pad((string) $data->COD_REG_FISCALE, 11, '0', STR_PAD_LEFT);
        $this->nuts2 = (string) $data->COD_NUTS2_2024;
    }

    public function checkProvinceData(stdClass $data): void
    {
        if ((int) $this->id !== (int) $data->COD_REG
            || $this->name !== $data->DEN_REG
        ) {
            throw new RuntimeException('Incompatible Province data');
        }
    }

    public function checkMunicipalityData(stdClass $data): void
    {
        if ((int) $this->id !== (int) $data->COD_REG
            || $this->name !== $data->DEN_REG
        ) {
            throw new RuntimeException('Incompatible Municipaliy data');
        }
    }

    public function serialize(Collator $collator): array
    {
        $result = [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type->value,
            'fiscalCode' => $this->fiscalCode,
        ];
        if ($this->nuts2 !== '') {
            $result['nuts2'] = $this->nuts2;
        }
        $children = array_map(
            static fn(Province $province): array => $province->serialize($collator),
            $this->provinces
        );
        usort($children, static fn(array $a, array $b) => $collator->compare($a['name'], $b['name']));
        $result['children'] = $children;

        return $result;
    }
}
