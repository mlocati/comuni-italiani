<?php

declare(strict_types=1);

namespace MLocati\ComuniItaliani;

interface Territory
{
    /**
     * @return string|int
     */
    public function getID();

    public function getParent(): ?Territory;

    public function getName(): string;

    public function __toString(): string;
}
