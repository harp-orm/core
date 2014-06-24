<?php

namespace Harp\Core\Test\Unit\Repo;

use Harp\Core\Repo\Event;

/**
 * @covers Harp\Core\Repo\Event
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class EventTest extends AbstractRepoTestCase
{
    public function testConstruct()
    {
        $events = [
            Event::CONSTRUCT,
            Event::INSERT,
            Event::UPDATE,
            Event::DELETE,
            Event::SAVE,
            Event::VALIDATE,
        ];

        $this->assertEquals(count($events), count(array_unique($events)), 'All events should be unique');
    }
}
