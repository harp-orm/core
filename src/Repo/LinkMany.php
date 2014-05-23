<?php

namespace CL\LunaCore\Repo;

use CL\LunaCore\Rel\AbstractRelMany;
use CL\LunaCore\Model\AbstractModel;
use CL\LunaCore\Model\Models;
use Countable;
use Iterator;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class LinkMany extends AbstractLink implements Countable, Iterator
{
    /**
     * @var Models
     */
    protected $original;

    /**
     * @var Models
     */
    protected $current;

    /**
     * @param AbstractRelMany $rel
     * @param AbstractModel[] $models
     */
    public function __construct(AbstractRelMany $rel, array $models)
    {
        $this->current = new Models($models);
        $this->original = new Models($models);

        parent::__construct($rel);
    }

    /**
     * @return AbstractRelMany
     */
    public function getRel()
    {
        return $this->rel;
    }

    /**
     * @return Models
     */
    public function get()
    {
        return $this->current;
    }

    /**
     * @return Models
     */
    public function getOriginal()
    {
        return $this->original;
    }

    /**
     * @return boolean
     */
    public function isChanged()
    {
        return $this->current != $this->original;
    }

    /**
     * @return Models
     */
    public function getAdded()
    {
        $added = clone $this->current;
        $added->removeAll($this->original);

        return $added;
    }

    /**
     * @return Models
     */
    public function getRemoved()
    {
        $removed = clone $this->original;
        $removed->removeAll($this->current);

        return $removed;
    }

    /**
     * @return Models
     */
    public function getCurrentAndOriginal()
    {
        $all = clone $this->current;
        $all->addAll($this->original);

        return $all;
    }

    /**
     * If no first, will return void model
     *
     * @return AbstractModel
     */
    public function getFirst()
    {
        return $this->current->getFirst() ?: $this->getRel()->getForeignRepo()->newVoidModel();
    }

    /**
     * @param  AbstractModel $model
     * @return Models
     */
    public function delete(AbstractModel $model)
    {
        return $this->getRel()->delete($model, $this);
    }

    /**
     * @param  AbstractModel $model
     * @return Models
     */
    public function insert(AbstractModel $model)
    {
        return $this->getRel()->insert($model, $this);
    }

    /**
     * @param AbstractModel $model
     */
    public function update(AbstractModel $model)
    {
        $this->getRel()->update($model, $this);
    }

    /**
     * @param  AbstractModel[] $models
     * @return LinkMany        $this
     */
    public function addArray(array $models)
    {
        $this->current->addArray($models);

        return $this;
    }

    /**
     * @param  AbstractModel $model
     * @return LinkMany      $this
     */
    public function add(AbstractModel $model)
    {
        $this->current->add($model);

        return $this;
    }

    /**
     * @param  AbstractModel $model
     * @return LinkMany      $this
     */
    public function remove(AbstractModel $model)
    {
        $this->current->remove($model);

        return $this;
    }

    /**
     * @return boolean
     */
    public function isEmpty()
    {
        return $this->current->isEmpty();
    }

    /**
     * @return LinkMany $this
     */
    public function clear()
    {
        $this->current->clear();

        return $this;
    }

    /**
     * @param  AbstractModel $model
     * @return boolean
     */
    public function has(AbstractModel $model)
    {
        return $this->current->has($model);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->current->toArray();
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->current->count();
    }

    /**
     * Implement Iterator
     *
     * @return AbstractModel
     */
    public function current()
    {
        return $this->current->current();
    }

    /**
     * Implement Iterator
     */
    public function key()
    {
        return $this->current->key();
    }

    /**
     * Implement Iterator
     */
    public function next()
    {
        return $this->current->next();
    }

    /**
     * Implement Iterator
     */
    public function rewind()
    {
        return $this->current->rewind();
    }

    /**
     * @return boolean
     */
    public function valid()
    {
        return $this->current->valid();
    }
}
