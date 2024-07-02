<?php

declare(strict_types=1);

namespace MLocati\ComuniItaliani\Test;

use PHPUnit\Framework\TestCase;
use MLocati\ComuniItaliani\Factory;
use MLocati\ComuniItaliani\Territory;
use MLocati\ComuniItaliani\TerritoryWithChildren;

class HierarchyTest extends TestCase
{
    public function testHierarchy(): void
    {
        $factory1 = new Factory();
        $factory2 = new Factory();

        $this->testHierarchyFor($factory1->getGeographicalSubdivisions()[0], $factory2->getGeographicalSubdivisions()[0]);
    }

    private function testHierarchyFor(Territory $territory, Territory $equalsNotSame, ?Territory $ascending = null): void
    {
        $this->assertNotSame($territory, $equalsNotSame);
        $this->assertTrue($territory->isSame($territory));
        $this->assertTrue($territory->isSame($equalsNotSame));
        $this->assertFalse($territory->isContainedIn($territory));
        $this->assertTrue($territory->isSameOrContainedIn($territory));
        if ($ascending !== null) {
            $this->assertTrue($ascending->contains($territory));
        }
        if ($territory instanceof TerritoryWithChildren) {
            if ($ascending !== null) {
                $this->assertFalse($territory->contains($ascending));
            }
            $this->assertFalse($territory->contains($territory));
            $this->assertFalse($territory->contains($equalsNotSame));
            $this->assertTrue($territory->isSameOrContains($territory));
            $this->assertTrue($territory->isSameOrContains($equalsNotSame));
            $child = $territory->getChildren()[0];
            $this->assertFalse($child->isSame($territory));
            $this->assertFalse($territory->isSame($child));
            $this->assertTrue($child->isContainedIn($territory));
            $this->assertTrue($child->isSameOrContainedIn($territory));
            $this->assertTrue($territory->contains($child));
            $this->assertTrue($territory->isSameOrContains($child));
            if ($child instanceof TerritoryWithChildren) {
                $this->assertFalse($child->contains($territory));
                $this->assertFalse($child->isSameOrContains($territory));
                $this->testHierarchyFor($child, $equalsNotSame->getChildren()[0], $ascending ?? $territory);
            }
        }
    }
}
