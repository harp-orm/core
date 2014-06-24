<?php

namespace Harp\Core\Model;

use LogicException;

/**
 * Add class property and methods to work with inheritence.
 * You need to call setInherited(true) on the corresponding repo
 *
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
trait InheritedTrait
{
    /**
     * @return \Harp\Core\Repo\AbstractRepo
     */
    abstract public function getRepo();

    /**
     * @var string
     */
    public $class;
}
