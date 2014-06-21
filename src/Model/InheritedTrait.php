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

    /**
     * Set the class property to the actual class
     * @throws LogicException If repo is not inherited
     */
    public function updateInheritanceClass()
    {
        $repo = $this->getRepo();

        if (! $repo->getInherited()) {
            throw new LogicException(
                sprintf('Repo %s must be "inherited"', $repo->getName())
            );
        }

        $this->class = $repo->getModelClass();

        return $this;
    }
}
