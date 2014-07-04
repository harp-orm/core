<?php

namespace Harp\Core\Test\Unit\Model;

use Harp\Core\Test\AbstractTestCase;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class InheritedTraitTest extends AbstractTestCase
{
    /**
     * @covers Harp\Core\Model\InheritedTrait
     */
    public function testUpdateInheritanceClass()
    {
        $model = new InheritedModel();

        $this->assertEquals('Harp\Core\Test\Unit\Model\InheritedModel', $model->class);

        $model = new InheritedModelBad();

        $this->assertNull($model->class);
    }
}
