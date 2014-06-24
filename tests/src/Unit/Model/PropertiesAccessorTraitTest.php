<?php

namespace Harp\Core\Test\Unit\Model;

use Harp\Core\Model\PropertiesAccessorTrait;
use Harp\Core\Test\AbstractTestCase;

/**
 * @coversDefaultClass Harp\Core\Model\PropertiesAccessorTrait
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class PropertiesAccessorTraitTest extends AbstractTestCase
{
    /**
     * @covers ::getProperties
     * @covers ::getPublicPropertiesOf
     */
    public function testGetProperties()
    {
        $object = new TestClassAccessor();

        $properties = $object->getProperties();
        $expected = [
            'public1' => 'test1',
            'public2' => 'test2',
        ];

        $this->assertEquals($expected, $properties);
    }

    /**
     * @covers ::setProperties
     */
    public function testSetProperties()
    {
        $object = new TestClassAccessor();

        $object->setProperties([
            'public1' => 'test1 change',
            'public2' => 'test2 change',
        ]);

        $this->assertEquals('test1 change', $object->public1);
        $this->assertEquals('test2 change', $object->public2);
    }
}
