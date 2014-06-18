<?php

namespace Harp\Core\Model;

use Harp\Validate\Errors;
use Harp\Core\Repo\AbstractLink;
use LogicException;

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

    /**
     * @return Harp\Core\Repo\AbstractRepo
     */
    abstract public function getRepo();

    /**
     * @var int
     */
    private $state;

    /**
     * @var array
     */
    private $errors;

    /**
     * @param array $properties
     * @param int   $state
     */
    public function __construct(array $properties = null, $state = null)
    {
        $this->setState($state ?: $this->getDefaultState());

        if (! empty($properties)) {
            $this->setProperties($properties);
        }

        $this->getRepo()->unserializeModel($this);

        $this->resetOriginals();
    }

    /**
     * @param  string $name
     * @return AbstractLink
     */
    public function getLink($name)
    {
        return $this->getRepo()->loadLink($this, $name);
    }

    /**
     * @return int
     */
    public function getDefaultState()
    {
        return $this->getId() ? State::SAVED : State::PENDING;
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
     * if the model has id, it becomes SAVED, otherwise - pending
     *
     * @return AbstractModel $this
     */
    public function setStateNotVoid()
    {
        if ($this->state === State::VOID) {
            $this->state = $this->getId() ? State::SAVED : State::PENDING;
        }

        return $this;
    }

    /**
     * @return AbstractModel $this
     */
    public function setStateVoid()
    {
        $this->state = State::VOID;

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
    public function isSaved()
    {
        return $this->state === State::SAVED;
    }

    /**
     * @return boolean
     */
    public function isPending()
    {
        return $this->state === State::PENDING;
    }

    /**
     * @return boolean
     */
    public function isDeleted()
    {
        return $this->state === State::DELETED;
    }

    /**
     * @return boolean
     */
    public function isSoftDeleted()
    {
        return false;
    }

    /**
     * @return boolean
     */
    public function isVoid()
    {
        return $this->state === State::VOID;
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
     * Set state as deleted (You need to save it to delete it from the repo)
     *
     * @return AbstractModel $this
     */
    public function delete()
    {
        if ($this->state === State::PENDING) {
            throw new LogicException('You cannot delete pending models');
        } elseif ($this->state === State::SAVED) {
            $this->state = State::DELETED;
        }

        return $this;
    }

    /**
     * Get Errors, fillid in when you call "validate"
     *
     * @return Errors
     */
    public function getErrors()
    {
        if (! $this->errors) {
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
        $changes = $this->getProperties();

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
