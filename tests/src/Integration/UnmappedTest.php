<?php

namespace CL\LunaCore\Test\Integration;

use CL\LunaCore\Test\Repo;

/**
 * @group integration
 * @group integration.unmapped
 * @coversNothing
 */
class UnmappedTest extends AbstractIntegrationTestCase
{
    public function testTest()
    {
        $user = Repo\User::get()->find(1);

        $this->assertEmpty($user->getUnmapped());

        $user->unmappedField = 'some value';
        $user->otherProp = 'val2';

        $this->assertEquals('some value', $user->unmappedField);

        $expected = [
            'unmappedField' => 'some value',
            'otherProp' => 'val2',
        ];

        $this->assertEquals($expected, $user->getUnmapped());
    }
}
