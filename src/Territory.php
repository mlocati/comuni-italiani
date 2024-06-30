<?php

declare(strict_types=1);

namespace MLocati\ComuniItaliani;

abstract class Territory
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return string|int
     */
    abstract public function getID();

    abstract public function getParent(): ?Territory;

    abstract public function getName(): string;

    abstract public function __toString(): string;
}
