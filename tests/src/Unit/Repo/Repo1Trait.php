<?php

namespace Harp\Core\Test\Unit\Repo;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
trait Repo1Trait
{
    public function initialize1Trait()
    {
        $this->initialize1TraitCalled = true;
    }
}
