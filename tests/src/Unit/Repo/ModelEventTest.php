<?php

namespace CL\LunaCore\Test\Unit\Repo;

use CL\LunaCore\Repo\ModelEvent;

class ModelEventTest extends AbstractRepoTestCase
{
    public function testConstruct()
    {
        $events = [
            ModelEvent::LOAD,
            ModelEvent::INSERT,
            ModelEvent::UPDATE,
            ModelEvent::DELETE,
            ModelEvent::SAVE,
            ModelEvent::VALIDATE,
        ];

        $this->assertEquals(count($events), count(array_unique($events)), 'All events should be unique');
    }
}
