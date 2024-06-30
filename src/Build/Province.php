<?php

declare(strict_types=1);

namespace MLocati\ComuniItaliani\Build;

use Collator;
use RuntimeException;
use stdClass;

/**
 * @internal
 */
final class Province
{
    public readonly string $id;

    public readonly string $name;

    public readonly string $oldID;

    public Province\Type $type;

    public readonly string $vehicleCode;

    public string $fiscalCode;

    public string $nuts3;


    /**
     * @var \MLocati\ComuniItaliani\Build\Municipality[]
     */
    public array $municipalities = [];

    public function __construct(
        public readonly Region $region,
        stdClass $data,
    ) {
        $this->id = str_pad((string) $data->COD_UTS, 3, '0', STR_PAD_LEFT);
        $this->name = $data->DEN_UTS;
        $this->oldID = str_pad((string) $data->COD_PROV_STORICO, 3, '0', STR_PAD_LEFT);
        $this->type = Province\Type::from($data->TIPO_UTS);
        $this->vehicleCode = $data->SIGLA_AUTOMOBILISTICA;
        $this->fiscalCode = str_pad((string) $data->COD_PROV_FISCALE, 11, '0', STR_PAD_LEFT);
        $this->nuts3 = $data->COD_NUTS3_2024;
    }

    public function checkMunicipalityData(stdClass $data): void
    {
        if ((int) $this->id !== (int) $data->COD_UTS
            || $this->name !== $data->DEN_UTS
            || (int) $this->oldID !== (int) $data->COD_PROV_STORICO
            || $this->type->value !== $data->TIPO_UTS
            || $this->vehicleCode !== $data->SIGLA_AUTOMOBILISTICA
        ) {
            throw new RuntimeException('Incompatible Municipaliy data');
        }
    }

    public function checkFlatEntry(FlatMunicipality $flatMunicipality): void
    {
        if ($this->id !== $flatMunicipality->utsID
            || $this->oldID !== $flatMunicipality->utsOldID
            || $this->vehicleCode !== $flatMunicipality->utsVehicleCode
            || $this->type !== $flatMunicipality->utsType
            || $this->name !== $flatMunicipality->utsName
        ) {
            throw new RuntimeException('Incompatible FlatMunicipality for ' . __CLASS__);
        }
    }

    public function serialize(Collator $collator): array
    {
        $result = [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type->value,
            'vehicleCode' => $this->vehicleCode,
            'fiscalCode' => $this->fiscalCode,
            'nuts3' => $this->nuts3,
        ];
        if ($this->oldID !== $this->id) {
            $result['oldID'] = $this->oldID;
        }
        $children = array_map(
            static fn(Municipality $municipality): array => $municipality->serialize($collator),
            $this->municipalities
        );
        usort($children, static fn(array $a, array $b) => $collator->compare($a['name'], $b['name']));
        $result['children'] = $children;

        return $result;
    }
}
