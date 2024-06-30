<?php

declare(strict_types=1);

namespace MLocati\ComuniItaliani;

final class GeographicalSubdivision extends TerritoryWithChildren
{
    private ?array $regions = null;

    /**
     * {@inheritdoc}
     *
     * @see \MLocati\ComuniItaliani\Territory::getParent()
     */
    public function getParent(): ?Territory
    {
        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @see \MLocati\ComuniItaliani\Territory::getID()
     */
    public function getID(): int
    {
        return $this->data['id'];
    }

    /**
     * {@inheritdoc}
     *
     * @see \MLocati\ComuniItaliani\Territory::getName()
     */
    public function getName(): string
    {
        return $this->data['name'];
    }

    public function getNuts1(): string
    {
        return $this->data['nuts1'];
    }

    /**
     * {@inheritdoc}
     *
     * @see \MLocati\ComuniItaliani\Territory::__toString()
     */
    public function __toString(): string
    {
        return $this->getName();
    }

    /**
     * {@inheritdoc}
     *
     * @see \MLocati\ComuniItaliani\TerritoryWithChildren::getChildren()
     */
    public function getChildren(): array
    {
        return $this->getRegions();
    }

    /**
     * @return \MLocati\ComuniItaliani\Region[]
     */
    public function getRegions(): array
    {
        return $this->regions ??= $this->buildRegions();
    }

    /**
     * @return \MLocati\ComuniItaliani\Region[]
     */
    private function buildRegions(): array
    {
        $result = array_map(
            function (array $data): Region {
                return new Region($data, $this);
            },
            $this->data['children']
        );

        unset($this->data['children']);

        return $result;
    }
}
