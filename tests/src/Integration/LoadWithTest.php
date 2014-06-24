<?php

namespace Harp\Core\Test\Integration;

use Harp\Core\Test\Model;

/**
 * @group integration
 * @group integration.load_with
 * @coversNothing
 */
class LoadWithTest extends AbstractIntegrationTestCase
{
    public function testTest()
    {
        $users = Model\User::findAll()
            ->loadWith(['posts']);
    }
}
