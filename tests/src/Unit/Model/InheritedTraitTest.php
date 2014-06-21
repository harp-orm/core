<?php

namespace Harp\Core\Test\Unit\Model;

use Harp\Core\Model\InheritedTrait;
use Harp\Core\Test\AbstractTestCase;

/**
 * @coversDefaultClass Harp\Core\Model\InheritedTrait
 */
class InheritedTraitTest extends AbstractTestCase
{
    /**
     * @covers ::updateInheritanceClass
     */
    public function testUpdateInheritanceClass()
    {
        $model = new InheritedModel();

        $this->assertEquals('Harp\Core\Test\Unit\Model\InheritedModel', $model->class);
    }

    /**
     * @covers ::updateInheritanceClass
     * @expectedException LogicException
     * @expectedExceptionMessage Repo InheritedModelBad must be "inherited"
     */
    public function testUpdateInheritanceClassError()
    {
        $model = new InheritedModelBad();
    }
}
