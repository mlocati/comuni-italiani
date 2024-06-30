<?php

declare(strict_types=1);

namespace MLocati\ComuniItaliani\Build;

use Collator;
use RuntimeException;
use stdClass;

/**
 * @internal
 */
final class Municipality
{
    public readonly string $id;

    public readonly string $name;

    public readonly string $nameIT;

    public readonly string $nameForeign;

    public readonly bool $isRegionalCapital;

    public readonly bool $isProvinceCapital;

    public readonly string $cadastralCode;

    public readonly string $fiscalCode;

    public readonly string $nuts1;

    public readonly string $nuts2;

    public readonly string $nuts3;

    public function __construct(
        public readonly Province $province,
        stdClass $data,
    ) {
        $this->id = str_pad((string) $data->PRO_COM_T, 6, '0', STR_PAD_LEFT);
        if ($data->PRO_COM !== (int) $this->id) {
            throw new RuntimeException('Data mismatch: PRO_COM vs PRO_COM_T');
        }
        $this->name = $data->COMUNE;
        $this->nameIT = $data->COMUNE_IT;
        $this->nameForeign = $data->COMUNE_A ?? '';
        $this->isRegionalCapital = $data->CC_REG === 1;
        $this->isProvinceCapital = $data->CC_UTS === 1;
        $this->cadastralCode = $data->COD_CATASTO;
        $fiscalCode = ltrim((string) $data->COD_COM_FISCALE, '0');
        $len = strlen($fiscalCode);
        if ($len > 0 && $len < 11) {
            $fiscalCode = str_pad($fiscalCode, 11, '0', STR_PAD_LEFT);
        }
        $this->fiscalCode = $fiscalCode;
        $this->nuts1 = $data->COM_NUTS1_2024;
        $this->nuts2 = $data->COM_NUTS2_2024;
        $this->nuts3 = $data->COM_NUTS3_2024;
    }

    public function serialize(Collator $collator): array
    {
        $idPrefix = $this->province->oldID;
        if (strlen($idPrefix) !== 3 || !str_starts_with($this->id, $idPrefix)) {
            throw new RuntimeException('Invalid ID prefix');
        }
        $result = [
            'id' => substr($this->id, 3),
            'name' => $this->name,
        ];
        if ($this->nameIT !== '' && $this->nameIT !== $this->name) {
            $result['nameIT'] = $this->nameIT;
        }
        if ($this->nameForeign !== '' && $this->nameForeign !== $this->name) {
            if (!isset($result['nameIT'])) {
                $m = null;
                if (!preg_match('_^(.*)[/-]\s*' . preg_quote($this->nameForeign, '_') . '$_', $this->name, $m)) {
                    throw new RuntimeException('Failed to extract the Italian name');
                }
                $result['nameIT'] = rtrim($m[1]);
            }
            $result['nameForeign'] = $this->nameForeign;
            if ($result['nameIT'] === $result['nameForeign']) {
                $result['name'] = $result['nameIT'];
                unset($result['nameIT'], $result['nameForeign']);
            }
        }
        if ($this->isRegionalCapital) {
            $result['isRegionalCapital'] = true;
        }
        if ($this->isProvinceCapital) {
            $result['isProvinceCapital'] = true;
        }
        $result['cadastralCode'] = $this->cadastralCode;
        if ($this->fiscalCode !== '') {
            $result['fiscalCode'] = $this->fiscalCode;
        }
        if ($this->nuts1 !== $this->province->region->geographicalSubdivision->nuts1) {
            $result['nuts1'] = $this->nuts1;
        }
        if ($this->nuts2 !== $this->province->region->nuts2) {
            $result['nuts2'] = $this->nuts2;
        }
        if ($this->nuts3 !== $this->province->nuts3) {
            $result['nuts3'] = $this->nuts3;
        }

        return $result;
    }
}
