<?php

namespace Harp\Core\Rel;

use Harp\Core\Model\AbstractModel;
use Harp\Core\Repo\LinkMany;
use InvalidArgumentException;

/**
 * Represents linking of one model to many other models. A basis for "set" or a "has many" association.
 * As result of the link is a LinkMany object. You can provide your own class that extends LinkMany,
 * giving the result more functionality.
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
abstract class AbstractRelMany extends AbstractRel
{
    /**
     * @var string
     */
    private $linkClass;

    /**
     * Set the class, that is returned for this relation. it must extend LinkMany
     *
     * @param string $class must extend Harp\Core\Repo\LinkMany
     */
    public function setLinkClass($class)
    {
        if (! is_subclass_of($class, 'Harp\Core\Repo\LinkMany')) {
            throw new InvalidArgumentException(
                sprintf('Class %s must be a subclass of Harp\Core\Repo\LinkMany', $class)
            );
        }

        $this->linkClass = $class;

        return $this;
    }

    /**
     * @return string
     */
    public function getLinkClass()
    {
        return $this->linkClass;
    }

    /**
     * Return a new LinkMany object holding the $linked models
     * Each linked model is passed through IdentityMap,
     * so that only unique objects are returned
     *
     * @param  AbstractModel $model
     * @param  array         $linked
     * @return LinkMany
     */
    public function newLinkFrom(AbstractModel $model, array $linked)
    {
        foreach ($linked as & $foreign) {
            $foreign = $foreign->getRepo()->getIdentityMap()->get($foreign);
        }

        if ($this->linkClass) {
            $class = $this->linkClass;

            return new $class($model, $this, $linked);
        } else {
            return new LinkMany($model, $this, $linked);
        }
    }
}
