<?php

namespace CL\LunaCore\Test\Unit\Model;

use CL\LunaCore\Model\State;
use CL\LunaCore\Test\AbstractTestCase;

/**
 * @covers CL\LunaCore\Model\State
 */
class StateTest extends AbstractTestCase
{
    public function testConstruct()
    {
        $events = [
            State::PENDING,
            State::SAVED,
            State::DELETED,
            State::VOID,
        ];

        $this->assertEquals(count($events), count(array_unique($events)), 'All states should be unique');
    }
}
