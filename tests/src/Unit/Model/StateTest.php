<?php

namespace Harp\Core\Test\Unit\Model;

use Harp\Core\Model\State;
use Harp\Core\Test\AbstractTestCase;

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
