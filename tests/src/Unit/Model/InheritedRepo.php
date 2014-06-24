<?php

namespace Harp\Core\Test\Unit\Model;

use Harp\Core\Repo\AbstractRepo;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class InheritedRepo extends AbstractRepo
{
    public function initialize()
    {
        $this
            ->setModelCLass(__NAMESPACE__.'\InheritedModel')
            ->setInherited(true);
    }
}
