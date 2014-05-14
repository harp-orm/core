<?php

namespace CL\LunaCore\Model;

use Closure;
use CL\Carpo\Errors;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractModel
{
    use DirtyTrackingTrait;
    use UnmappedPropertiesTrait;
    use PropertiesAccessorTrait;

    const PENDING = 1;
    const DELETED = 2;
    const PERSISTED = 3;
    const VOID = 4;

    /**
     * @return CL\LunaCore\Repo\AbstractRepo
     */
    abstract public function getRepo();

    private $state;
    private $errors;

    public function __construct(array $properties = null, $state = self::PENDING)
    {
        $this->state = $state;

        if ($properties) {
            $this->setProperties($properties);
        }

        $this->resetOriginals();
    }

    /**
     * Set originals to current properties of the model
     *
     * @return AbstractModel $this
     */
    public function resetOriginals()
    {
        $this->setOriginals($this->getProperties());

        return $this;
    }

    /**
     * if the model has id, it becomes persisted, otherwise - pending
     *
     * @return AbstractModel $this
     */
    public function setStateNotVoid()
    {
        if ($this->state === self::VOID) {
            $this->state = $this->getId() ? self::PERSISTED : self::PENDING;
        }

        return $this;
    }

    /**
     * @return AbstractModel $this
     */
    public function setStateVoid()
    {
        $this->state = self::VOID;

        return $this;
    }

    /**
     * @return AbstractModel $this
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return int ModelState
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return boolean
     */
    public function isPersisted()
    {
        return $this->state === self::PERSISTED;
    }

    /**
     * @return boolean
     */
    public function isPending()
    {
        return $this->state === self::PENDING;
    }

    /**
     * @return boolean
     */
    public function isDeleted()
    {
        return $this->state === self::DELETED;
    }

    /**
     * @return boolean
     */
    public function isVoid()
    {
        return $this->state === self::VOID;
    }

    /**
     * Property defined by Repo Primary Key
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->{$this->getRepo()->getPrimaryKey()};
    }

    /**
     * Set property defined by Repo Primary Key
     *
     * @param mixed
     * @return AbstractModel $this
     */
    public function setId($id)
    {
        $this->{$this->getRepo()->getPrimaryKey()} = $id;

        return $this;
    }

    /**
     * Set state as deleted (You need to persist it to delete it from the repo)
     *
     * @return AbstractModel $this
     */
    public function delete()
    {
        $this->state = self::DELETED;

        return $this;
    }

    /**
     * Get Errors, fillid in when you call "validate"
     *
     * @return Errors
     */
    public function getErrors()
    {
        if ( ! $this->errors) {
            $this->errors = new Errors($this);
        }

        return $this->errors;
    }

    /**
     * Call execute on Repo's Assertions for this model
     * Use only changes and unmapped
     *
     * @return boolean is empty errors
     */
    public function validate()
    {
        $changes = $this->getChanges();

        if ($this->getUnmapped()) {
            $changes += $this->getUnmapped();
        }

        $this->errors = $this->getRepo()->getAsserts()->validate($changes);

        return $this->isEmptyErrors();
    }

    /**
     * @return boolean
     */
    public function isEmptyErrors()
    {
        return $this->errors ? $this->errors->isEmpty() : true;
    }
}
