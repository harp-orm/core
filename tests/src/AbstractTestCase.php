<?php

namespace Harp\Core\Test;

use Harp\Core\Repo\Container;
use PHPUnit_Framework_TestCase;

abstract class AbstractTestCase extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();

        Container::clear();
    }
}
