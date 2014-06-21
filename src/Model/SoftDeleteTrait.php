<?php

namespace Harp\Core\Model;

/**
 * Add deletedAt property and methods to work with soft deletion.
 * Also overrides several getDefaultState, delete, isSoftDeleted to return appropriate values,
 * if the model is "soft deleted"
 * You need to call setSoftDelete(true) on the corresponding repo
 *
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
trait SoftDeleteTrait
{
    /**
     * @param int $state
     */
    abstract public function setState($state);

    /**
     * @var int
     */
    public $deletedAt;

    /**
     * @return SoftDeleteTrait $this
     */
    public function delete()
    {
        $this->deletedAt = time();

        parent::delete();

        return $this;
    }

    /**
     * @return int
     */
    public function getDefaultState()
    {
        return $this->deletedAt ? State::DELETED : parent::getDefaultState();
    }

    /**
     * @return SoftDeleteTrait $this
     */
    public function realDelete()
    {
        $this->deletedAt = null;

        parent::delete();

        return $this;
    }

    /**
     * @return SoftDeleteTrait $this
     */
    public function restore()
    {
        $this->deletedAt = null;
        $this->setState(State::SAVED);

        return $this;
    }

    /**
     * @return boolean
     */
    public function isSoftDeleted()
    {
        return ($this->isDeleted() and $this->deletedAt !== null);
    }
}
