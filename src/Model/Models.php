<?php

namespace CL\LunaCore\Model;

use CL\Util\Objects;
use SplObjectStorage;
use InvalidArgumentException;
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
     * @param  AbstractModel[]  $models
     * @return Models
     */
    public static function fromArray(array $models)
    {
        $new = new Models();
        return $new->addArray($models);
    }

    /**
     * @var array
     */
    private $models;

    /**
     * @var string
     */
    private $class;

    public function __construct(SplObjectStorage $models = null, $class = null)
    {
        $this->models = new SplObjectStorage();
        $this->models->rewind();

        if ($models) {
            $this->set($models);
        }

        $this->class = $class;
    }

    public function set(SplObjectStorage $models)
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

    public function isAccepted(AbstractModel $model)
    {
        return (! $this->class or ($model instanceof $this->class));
    }

    public function add(AbstractModel $model)
    {
        if (! $this->isAccepted($model)) {
            throw new InvalidArgumentException(
                sprintf('Model must be an instance of %s', $this->class)
            );
        }

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
        return new Models(
            Objects::filter($this->models, $filter),
            $this->class
        );
    }

    public function byRepo()
    {
        $repos = Objects::groupBy($this->models, function (AbstractModel $model) {
            return $model->getRepo();
        });

        foreach ($repos as $repo) {
            yield $repo => new Models($repos->getInfo());
        }
    }

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
