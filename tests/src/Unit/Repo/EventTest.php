<?php

namespace CL\LunaCore\Test\Unit\Repo;

use CL\LunaCore\Repo\Event;

/**
 * @covers CL\LunaCore\Repo\Event
 */
class EventTest extends AbstractRepoTestCase
{
    public function testConstruct()
    {
        $events = [
            Event::LOAD,
            Event::INSERT,
            Event::UPDATE,
            Event::DELETE,
            Event::SAVE,
            Event::VALIDATE,
        ];

        $this->assertEquals(count($events), count(array_unique($events)), 'All events should be unique');
    }
}
