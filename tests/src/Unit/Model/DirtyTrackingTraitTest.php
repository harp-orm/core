<?php

namespace CL\LunaCore\Test;

use CL\LunaCore\Model\DirtyTrackingTrait;

class DirtyTrackingTraitTest extends AbstractTestCase
{
    public function testOriginals()
    {
        $trait = $this->getMockForTrait('CL\LunaCore\Model\DirtyTrackingTrait');

        $originals = $trait->getOriginals();

        $this->assertEmpty($originals);

        $expected = [
            'test' => 'test1',
            'test2' => 'test2',
        ];

        $trait->setOriginals($expected);

        $this->assertEquals($expected, $trait->getOriginals());
    }
}
