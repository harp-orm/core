<?php

namespace Harp\Core\Test\Unit\Repo;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
trait Repo1Trait
{
    public function initialize1Trait()
    {
        $this->initialize1TraitCalled = true;
    }
}
