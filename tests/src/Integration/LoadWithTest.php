<?php

namespace Harp\Core\Test\Integration;

use Harp\Core\Test\Model;

/**
 * @group integration
 * @group integration.load_with
 * @coversNothing
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class LoadWithTest extends AbstractIntegrationTestCase
{
    public function testTest()
    {
        $users = Model\User::findAll()
            ->loadWith(['posts']);
    }
}
