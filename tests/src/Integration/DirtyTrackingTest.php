<?php

namespace Harp\Core\Test\Integration;

use Harp\Core\Test\Model;

/**
 * @group integration
 * @group integration.dirty_tracking
 * @coversNothing
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class DirtyTrackingTest extends AbstractIntegrationTestCase
{
    public function testTest()
    {
        $user = Model\User::find(1);
        $name = $user->name;

        $this->assertFalse($user->isChanged());
        $this->assertEmpty($user->getChanges());

        $user->name = 'changed';
        $user->isBlocked = false;

        $this->assertTrue($user->isChanged());
        $this->assertTrue($user->hasChange('name'));
        $this->assertTrue($user->hasChange('isBlocked'));
        $this->assertEquals($name, $user->getOriginal('name'));
        $this->assertEquals(true, $user->getOriginal('isBlocked'));
        $this->assertEquals(['name' => 'changed', 'isBlocked' => false], $user->getChanges());

        $user->name = $name;

        $this->assertTrue($user->isChanged());
        $this->assertFalse($user->hasChange('name'));
        $this->assertTrue($user->hasChange('isBlocked'));
        $this->assertEquals(['isBlocked' => false], $user->getChanges());

        $user->isBlocked = true;

        $this->assertFalse($user->isChanged());
        $this->assertEmpty($user->getChanges());
    }
}
