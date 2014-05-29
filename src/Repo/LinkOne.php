<?php

namespace Harp\Core\Repo;

use Harp\Core\Rel\AbstractRelOne;
use Harp\Core\Rel\DeleteOneInterface;
use Harp\Core\Rel\InsertOneInterface;
use Harp\Core\Rel\UpdateOneInterface;
use Harp\Core\Model\AbstractModel;
use Harp\Core\Model\Models;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class LinkOne extends AbstractLink
{
    /**
     * @var AbstractModel
     */
    protected $current;

    /**
     * @var AbstractModel
     */
    protected $original;

    /**
     * @param AbstractRelOne $rel
     * @param AbstractModel  $current
     */
    public function __construct(AbstractRelOne $rel, AbstractModel $current)
    {
        $this->current = $current;
        $this->original = $current;

        parent::__construct($rel);
    }

    /**
     * @return AbstractRelOne
     */
    public function getRel()
    {
        return $this->rel;
    }

    /**
     * @param  AbstractModel $current
     * @return LinkOne       $this
     */
    public function set(AbstractModel $current)
    {
        $this->current = $current;

        return $this;
    }

    /**
     * @return LinkOne $this
     */
    public function clear()
    {
        $this->current->setStateVoid();

        return $this;
    }

    /**
     * @return AbstractModel current
     */
    public function get()
    {
        return $this->current;
    }

    /**
     * @return AbstractModel
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
        return $this->current !== $this->original;
    }

    /**
     * @return Models
     */
    public function getCurrentAndOriginal()
    {
        return new Models([$this->current, $this->original]);
    }

    /**
     * @param  AbstractModel $model
     * @return Models
     */
    public function delete(AbstractModel $model)
    {
        $rel = $this->getRel();
        if ($rel instanceof DeleteOneInterface) {
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
        if ($rel instanceof InsertOneInterface) {
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
        if ($rel instanceof UpdateOneInterface) {
            return $rel->update($model, $this);
        }
    }
}
