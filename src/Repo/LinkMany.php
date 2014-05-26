<?php

namespace CL\LunaCore\Repo;

use CL\LunaCore\Rel\AbstractRelMany;
use CL\LunaCore\Rel\DeleteManyInterface;
use CL\LunaCore\Rel\InsertManyInterface;
use CL\LunaCore\Rel\UpdateManyInterface;
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
        $this->current = new RepoModels($rel->getForeignRepo(), $models);
        $this->original = new RepoModels($rel->getForeignRepo(), $models);

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
        return $this->current->getFirst();
    }

    /**
     * Return next model, void model if no model
     *
     * @return AbstractModel
     */
    public function getNext()
    {
        return $this->current->getNext();
    }

    /**
     * @param  AbstractModel $model
     * @return Models
     */
    public function delete(AbstractModel $model)
    {
        $rel = $this->getRel();
        if ($rel instanceof DeleteManyInterface) {
            return $rel->delete($model, $this);
        } else {
            return new Models();
        }
    }

    /**
     * @param  AbstractModel $model
     * @return Models
     */
    public function insert(AbstractModel $model)
    {
        $rel = $this->getRel();
        if ($rel instanceof InsertManyInterface) {
            return $rel->insert($model, $this);
        } else {
            return new Models();
        }
    }

    /**
     * @param AbstractModel $model
     */
    public function update(AbstractModel $model)
    {
        $rel = $this->getRel();
        if ($rel instanceof UpdateManyInterface) {
            return $this->getRel()->update($model, $this);
        }
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
        $this->current->next();

        return $this;
    }

    /**
     * Implement Iterator
     */
    public function rewind()
    {
        $this->current->rewind();

        return $this;
    }

    /**
     * @return boolean
     */
    public function valid()
    {
        return $this->current->valid();
    }
}
