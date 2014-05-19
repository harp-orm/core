<?php

namespace CL\LunaCore\Repo;

use CL\LunaCore\Rel\AbstractRelMany;
use CL\LunaCore\Model\AbstractModel;
use CL\Util\Objects;
use Countable;
use Iterator;
use SplObjectStorage;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class LinkMany extends AbstractLink implements Countable, Iterator
{
    /**
     * @var SplObjectStorage
     */
    protected $current;

    /**
     * @var SplObjectStorage
     */
    protected $originals;

    /**
     * @param AbstractRelMany $rel
     * @param AbstractModel[] $current
     */
    public function __construct(AbstractRelMany $rel, array $current)
    {
        parent::__construct($rel);

        $this->current = new SplObjectStorage();

        $this->addArray($current);

        $this->originals = clone $this->current;
    }

    /**
     * @param  AbstractModel $model
     * @return LinkMany
     */
    public function add(AbstractModel $model)
    {
        $this->current->attach($model);

        return $this;
    }

    /**
     * @param  AbstractModel[] $models
     * @return LinkMany
     */
    public function addArray(array $models)
    {
        foreach ($models as $item) {
            $this->add($item);
        }

        return $this;
    }

    /**
     * @param  AbstractModel[] $models
     * @return LinkMany        $this
     */
    public function set(array $models)
    {
        $this->clear();
        $this->addArray($models);

        return $this;
    }

    /**
     * @return LinkMany
     */
    public function clear()
    {
        $this->current = new SplObjectStorage();

        return $this;
    }

    /**
     * @param  AbstractModel $model
     * @return LinkMany
     */
    public function remove(AbstractModel $model)
    {
        unset($this->current[$model]);

        return $this;
    }

    /**
     * @return boolean
     */
    public function isEmpty()
    {
        return count($this->current) === 0;
    }

    /**
     * @param  AbstractModel $model
     * @return boolean
     */
    public function has(AbstractModel $model)
    {
        return $this->current->contains($model);
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
     * @return SplObjectStorage
     */
    public function all()
    {
        return $this->current;
    }

    /**
     * @return AbstractModel[]
     */
    public function toArray()
    {
        return Objects::toArray($this->current);
    }

    /**
     * @return SplObjectStorage
     */
    public function getOriginals()
    {
        return $this->originals;
    }

    /**
     * @return array
     */
    public function getOriginalIds()
    {
        return Objects::invoke($this->originals, 'getId');
    }

    /**
     * @return array
     */
    public function getIds()
    {
        return Objects::invoke($this->current, 'getId');
    }

    /**
     * @return SplObjectStorage
     */
    public function getAdded()
    {
        $added = clone $this->current;
        $added->removeAll($this->originals);

        return $added;
    }

    /**
     * @return array
     */
    public function getAddedIds()
    {
        return Objects::invoke($this->getAdded(), 'getId');
    }

    /**
     * @return SplObjectStorage
     */
    public function getRemoved()
    {
        $removed = clone $this->originals;
        $removed->removeAll($this->current);

        return $removed;
    }

    /**
     * @return array
     */
    public function getRemovedIds()
    {
        return Objects::invoke($this->getRemoved(), 'getId');
    }

    /**
     * @return SplObjectStorage
     */
    public function getCurrentAndOriginal()
    {
        $all = clone $this->current;
        $all->addAll($this->originals);

        return $all;
    }

    /**
     * If no first, will return void model
     *
     * @return AbstractModel
     */
    public function getFirst()
    {
        $this->current->rewind();

        if ($this->current->valid()) {
            return $this->current->current();
        } else {
            return $this->getRel()->getForeignRepo()->newVoidInstance();
        }
    }

    /**
     * Implements Countable
     *
     * @return int
     */
    public function count()
    {
        return $this->current->count();
    }

    /**
     * Implements Iterator
     *
     * @return AbstractModel
     */
    public function current()
    {
        return $this->current->current();
    }

    /**
     * Implements Iterator
     */
    public function key()
    {
        return $this->current->key();
    }

    /**
     * Implements Iterator
     *
     * @return AbstractModel
     */
    public function next()
    {
        return $this->current->next();
    }

    /**
     * Implements Iterator
     *
     * @return LinkMany $this
     */
    public function rewind()
    {
        $this->current->rewind();

        return $this;
    }

    /**
     * Implements Iterator
     *
     * @return boolean
     */
    public function valid()
    {
        return $this->current->valid();
    }
}
