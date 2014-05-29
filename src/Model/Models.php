<?php

namespace Harp\Core\Model;

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
     * @var SplObjectStorage
     */
    private $models;

    public function __construct(array $models = null)
    {
        $this->models = new SplObjectStorage();

        if ($models) {
            $this->addArray($models);
        }
    }

    /**
     * Clone internal SplObjectStorage
     */
    public function __clone()
    {
        $this->models = clone $this->models;
    }

    /**
     * @param  SplObjectStorage $models
     * @return Models           $this
     */
    public function addObjects(SplObjectStorage $models)
    {
        foreach ($models as $model) {
            $this->add($model);
        }

        return $this;
    }

    public function addAll(Models $other)
    {
        if ($other->count() > 0) {
            $this->models->addAll($other->models);
        }

        return $this;
    }

    /**
     * @param  array  $models
     * @return Models $this
     */
    public function addArray(array $models)
    {
        foreach ($models as $model) {
            $this->add($model);
        }

        return $this;
    }

    /**
     * @param  AbstractModel $model
     * @return Models        $this
     */
    public function add(AbstractModel $model)
    {
        $this->models->attach($model);

        return $this;
    }

    /**
     * @return Models $this
     */
    public function clear()
    {
        $this->models = new SplObjectStorage();

        return $this;
    }

    /**
     * @param  AbstractModel $model
     * @return boolean
     */
    public function has(AbstractModel $model)
    {
        return $this->models->contains($model);
    }

    /**
     * @param  string|integer $id
     * @return boolean
     */
    public function hasId($id)
    {
        return array_search($id, $this->getIds()) !== false;
    }

    /**
     * @return AbstractModel|null
     */
    public function getFirst()
    {
        $this->models->rewind();

        return $this->models->current();
    }

    /**
     * @return AbstractModel|null
     */
    public function getNext()
    {
        $this->models->next();

        return $this->models->current();
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->models->count();
    }

    /**
     * Return containing models
     *
     * @return SplObjectStorage
     */
    public function all()
    {
        return $this->models;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return Objects::toArray($this->models);
    }

    /**
     * @param  AbstractModel $model
     * @return Models        $this
     */
    public function remove(AbstractModel $model)
    {
        unset($this->models[$model]);

        return $this;
    }

    /**
     * @param  Models $models
     * @return Models $this
     */
    public function removeAll(Models $models)
    {
        $this->models->removeAll($models->all());

        return $this;
    }

    /**
     * @return boolean
     */
    public function isEmpty()
    {
        return count($this->models) === 0;
    }

    /**
     * @param  Closure $filter must return true for each item
     * @return Models  Filtered models
     */
    public function filter(Closure $filter)
    {
        $filtered = new Models();

        $filtered->addObjects(Objects::filter($this->models, $filter));

        return $filtered;
    }

    /**
     * Group models by repo, call yield for each repo
     *
     * @param Closure $yield Call for each repo ($repo, $models)
     */
    public function byRepo(Closure $yield)
    {
        $repos = Objects::groupBy($this->models, function (AbstractModel $model) {
            return $model->getRepo();
        });

        foreach ($repos as $repo) {
            $models = new Models();
            $models->addObjects($repos->getInfo());

            $yield($repo, $models);
        }
    }

    /**
     * @param  string $property
     * @return array
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
     * @param  string  $property
     * @return boolean
     */
    public function isEmptyProperty($property)
    {
        foreach ($this->models as $model) {
            if ($model->$property) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  string $property
     * @return array
     */
    public function pluckPropertyUnique($property)
    {
        return array_unique(array_filter($this->pluckProperty($property)));
    }

    /**
     * @return array
     */
    public function getIds()
    {
        return Objects::invoke($this->models, 'getId');
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
     *
     * @return Models
     */
    public function next()
    {
        $this->models->next();

        return $this;
    }

    /**
     * Implement Iterator
     */
    public function rewind()
    {
        $this->models->rewind();

        return $this;
    }

    /**
     * @return boolean
     */
    public function valid()
    {
        return $this->models->valid();
    }
}
