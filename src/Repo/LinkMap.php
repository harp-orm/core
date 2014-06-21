<?php

namespace Harp\Core\Repo;

use Harp\Core\Model\AbstractModel;
use SplObjectStorage;
use InvalidArgumentException;

/**
 * Hold all the links between models. It is useful to hold this information outside of the models
 * themselves (in SplObjectStorage), So that the models' footprint remain as small as possible.
 *
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class LinkMap
{
    /**
     * @var SplObjectStorage
     */
    private $map;

    /**
     * @var AbstractRepo
     */
    private $repo;

    /**
     * @param AbstractRepo $repo
     */
    public function __construct(AbstractRepo $repo)
    {
        $this->map = new SplObjectStorage();
        $this->repo = $repo;
    }

    /**
     * @return AbstractRepo
     */
    public function getRepo()
    {
        return $this->repo;
    }

    /**
     * Get Links object associated with this model.
     * If there is none, an empty Links object is created.
     *
     * @param  AbstractModel            $model
     * @return Links
     * @throws InvalidArgumentException If model does not belong to repo
     */
    public function get(AbstractModel $model)
    {
        if (! $this->repo->isModel($model)) {
            throw new InvalidArgumentException(
                sprintf('Model must be %s, was %s', $this->repo->getModelClass(), get_class($model))
            );
        }

        if ($this->map->contains($model)) {
            return $this->map[$model];
        } else {
            return $this->map[$model] = new Links($model);
        }
    }

    /**
     * @param AbstractLink $link
     * @return LinkMap $this
     */
    public function addLink(AbstractLink $link)
    {
        $this->get($link->getModel())->add($link);

        return $this;
    }

    /**
     * @param  AbstractModel $model
     * @return boolean
     */
    public function isEmpty(AbstractModel $model)
    {
        return (! $this->map->contains($model) or $this->map[$model]->isEmpty());
    }

    /**
     * @param  AbstractModel $model
     * @return boolean
     */
    public function has(AbstractModel $model)
    {
        return $this->map->contains($model);
    }

    /**
     * @return LinkMap $this
     */
    public function clear()
    {
        $this->map = new SplObjectStorage();

        return $this;
    }
}
