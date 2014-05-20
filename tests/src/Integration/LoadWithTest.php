<?php

namespace CL\LunaCore\Test\Integration;

use CL\LunaCore\Test\Model;
use CL\LunaCore\Test\Repo;
use CL\LunaCore\Save\Save;

/**
 * @group integration
 * @group integration.load_with
 * @coversNothing
 */
class LoadWithTest extends AbstractIntegrationTestCase
{
    public function testTest()
    {
        $users = Repo\User::get()
            ->findAll()
            ->loadWith(['posts']);
    }
}
