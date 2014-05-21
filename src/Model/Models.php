<?php

namespace CL\LunaCore\Model;

use CL\Util\Objects;
use SplObjectStorage;
use Closure;
use Countable;
use Iterator;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Models implements Countable, Iterator
{
    /**
     * @param  SplObjectStorage  $models
     * @return Models
     */
    public static function fromObjects(SplObjectStorage $models)
    {
        $new = new Models();
        return $new->addObjects($models);
    }

    /**
     * @var array
     */
    private $models;


    public function __construct(array $models = null)
    {
        $this->models = new SplObjectStorage();
        $this->models->rewind();

        if ($models) {
            $this->addArray($models);
        }
    }

    public function addObjects(SplObjectStorage $models)
    {
        foreach ($models as $model) {
            $this->add($model);
        }

        return $this;
    }

    public function addArray(array $models)
    {
        foreach ($models as $model) {
            $this->add($model);
        }

        return $this;
    }

    public function add(AbstractModel $model)
    {
        $this->models->attach($model);

        return $this;
    }

    public function clear()
    {
        $this->models = new SplObjectStorage();

        return $this;
    }

    public function has(AbstractModel $model)
    {
        return $this->models->contains($model);
    }

    public function count()
    {
        return $this->models->count();
    }

    public function all()
    {
        return $this->models;
    }

    public function toArray()
    {
        return Objects::toArray($this->models);
    }

    public function remove(AbstractModel $model)
    {
        unset($this->models[$model]);

        return $this;
    }

    public function removeAll(Models $models)
    {
        $this->models->removeAll($models->all());

        return $this;
    }

    public function isEmpty()
    {
        return count($this->models) === 0;
    }

    public function filter(Closure $filter)
    {
        $filtered = new Models();

        $filtered->addObjects(Objects::filter($this->models, $filter));

        return $filtered;
    }

    public function byRepo(Closure $yield)
    {
        $repos = Objects::groupBy($this->models, function (AbstractModel $model) {
            return $model->getRepo();
        });

        foreach ($repos as $repo) {
            $yield($repo, Models::fromObjects($repos->getInfo()));
        }
    }

    /**
     * @param string $property
     */
    public function pluckProperty($property)
    {
        $values = [];

        foreach ($this->models as $model) {
            $values []= $model->$property;
        }

        return $values;
    }

    /**
     * Implement Iterator
     *
     * @return AbstractModel
     */
    public function current()
    {
        return $this->models->current();
    }

    /**
     * Implement Iterator
     */
    public function key()
    {
        return $this->models->key();
    }

    /**
     * Implement Iterator
     */
    public function next()
    {
        return $this->models->next();
    }

    /**
     * Implement Iterator
     */
    public function rewind()
    {
        return $this->models->rewind();
    }

    /**
     * @return boolean
     */
    public function valid()
    {
        return $this->models->valid();
    }
}
