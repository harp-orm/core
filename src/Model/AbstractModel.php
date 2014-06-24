<?php

namespace Harp\Core\Model;

use Harp\Validate\Errors;
use Harp\Core\Repo\Event;
use LogicException;

/**
 * The class that all models should extend.
 * Some of its generic functionality has been extracted to traits so it can be added to other classes
 *
 * Each model should be able to reference its repo, thats why you will need to implement getRepo method.
 *
 * Each model has several different "states". State::PENDING, State::DELETED, State::SAVED are used in the
 * model's persistence lifesycle. State::VOID is a special state signifying a non-existant "Null" model.
 * Its useful as void models cannot be saved, but retain all of their functionality.
 *
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractModel
{
    use DirtyTrackingTrait;
    use UnmappedPropertiesTrait;
    use PropertiesAccessorTrait;
    use RepoConnectionTrait;

    /**
     * @var int
     */
    private $state;

    /**
     * @var Errors
     */
    private $errors;

    /**
     * Set properties / state, unserialize properties and set original properties.
     *
     * @param array $properties
     * @param int   $state
     */
    public function __construct(array $properties = null, $state = null)
    {
        $this->setState($state ?: $this->getDefaultState());

        if (! empty($properties)) {
            $this->setProperties($properties);
        }

        $this->getRepo()->getSerializers()->unserialize($this);

        $this
            ->updateInheritanceClass()
            ->resetOriginals();

        $this->getRepo()->dispatchAfterEvent($this, Event::CONSTRUCT);
    }

    /**
     * Default state of models with "id" is State::SAVED, otherwise - State::PENDING
     *
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
     * Void models will not be saved.
     *
     * @return AbstractModel $this
     */
    public function setStateVoid()
    {
        $this->state = State::VOID;

        return $this;
    }

    /**
     * @param  int           $state
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
    public function isVoid()
    {
        return $this->state === State::VOID;
    }

    /**
     * This method will be overridden by SoftDeleteTrait
     *
     * @return boolean
     */
    public function isSoftDeleted()
    {
        return false;
    }

    /**
     * A no-op, will be overridden by InheritedTrait
     *
     * @return AbstractModel $this
     */
    public function updateInheritanceClass()
    {
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
     * @return boolean
     */
    public function validate()
    {
        $this->errors = $this->getRepo()->getAsserts()->validate($this);

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
