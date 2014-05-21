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
     * @param  SplObjectStorage $models
     * @return Models
     */
    public static function fromObjects(SplObjectStorage $models)
    {
        $new = new Models();

        return $new->addObjects($models);
    }

    /**
     * @var SplObjectStorage
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
            $yield($repo, Models::fromObjects($repos->getInfo()));
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
