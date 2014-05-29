<?php

namespace Harp\Core\Test\Integration;

use Harp\Core\Test\Model;
use Harp\Core\Test\Repo;
use Harp\Core\Save\Save;

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
