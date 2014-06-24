<?php

namespace Harp\Core\Test\Unit\Model;

use Harp\Core\Model\State;
use Harp\Core\Test\AbstractTestCase;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
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
