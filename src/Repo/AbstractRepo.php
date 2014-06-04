<?php

namespace Harp\Core\Repo;

use Harp\Validate\Asserts;
use Harp\Core\Model\AbstractModel;
use Harp\Core\Model\State;
use Harp\Core\Rel\AbstractRel;
use ReflectionClass;
use InvalidArgumentException;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractRepo
{
    abstract public function initialize();

    /**
     * @var string
     */
    private $modelClass;

    /**
     * @var IdentityMap
     */
    private $identityMap;

    /**
     * @var LinkMap
     */
    private $linkMap;

    /**
     * @var ReflectionClass
     */
    private $modelReflection;

    /**
     * @var EventListeners
     */
    private $eventListeners;

    /**
     * @var string
     */
    private $primaryKey = 'id';

    /**
     * @var string
     */
    private $nameKey = 'name';

    /**
     * @var AbstractRel[]
     */
    private $rels = [];

    /**
     * @var Asserts
     */
    private $asserts;

    /**
     * @var boolean
     */
    private $initialized = false;

    /**
     * @var boolean
     */
    private $softDelete = false;

    /**
     * @var boolean
     */
    private $inherited = false;

    /**
     * @var AbstractRepo
     */
    private $rootRepo;

    /**
     * @param string $modelClass
     */
    public function __construct($modelClass)
    {
        $this->modelClass = $modelClass;
        $this->linkMap = new LinkMap($this);
        $this->eventListeners = new EventListeners();
        $this->asserts = new Asserts();
        $this->modelReflection = new ReflectionClass($modelClass);
        $this->identityMap = new IdentityMap($this);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->modelReflection->getShortName();
    }

    /**
     * @return IdentityMap
     */
    public function getIdentityMap()
    {
        return $this->identityMap;
    }

    /**
     * @return LinkMap
     */
    public function getLinkMap()
    {
        return $this->linkMap;
    }

    /**
     * @return string
     */
    public function getModelClass()
    {
        return $this->modelClass;
    }

    /**
     * @return AbstractRepo
     */
    public function getRootRepo()
    {
        $this->initializeOnce();

        return $this->rootRepo ?: $this;
    }

    /**
     * @param  AbstractRepo $rootRepo
     * @return AbstractRepo $this
     */
    public function setRootRepo(AbstractRepo $rootRepo)
    {
        $this->rootRepo = $rootRepo;

        return $this;
    }

    /**
     * @return ReflectionClass
     */
    public function getModelReflection()
    {
        return $this->modelReflection;
    }

    /**
     * @return boolean
     */
    public function getSoftDelete()
    {
        $this->initializeOnce();

        return $this->softDelete;
    }

    /**
     * @param  boolean      $softDelete
     * @return AbstractRepo $this
     */
    public function setSoftDelete($softDelete)
    {
        $this->softDelete = (bool) $softDelete;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getInherited()
    {
        $this->initializeOnce();

        return $this->inherited;
    }

    /**
     * @param  boolean      $inherited
     * @return AbstractRepo $this
     */
    public function setInherited($inherited)
    {
        $this->inherited = (bool) $inherited;

        return $this;
    }

    /**
     * @return string
     */
    public function getPrimaryKey()
    {
        $this->initializeOnce();

        return $this->primaryKey;
    }

    /**
     * @param string
     * @return AbstractRepo $this
     */
    public function setPrimaryKey($primaryKey)
    {
        $this->primaryKey = $primaryKey;

        return $this;
    }

    /**
     * @return string
     */
    public function getNameKey()
    {
        $this->initializeOnce();

        return $this->nameKey;
    }

    /**
     * @param string
     * @return AbstractRepo $this
     */
    public function setNameKey($nameKey)
    {
        $this->nameKey = $nameKey;

        return $this;
    }

    /**
     * @param  AbstractRel  $rel
     * @return AbstractRepo $this
     */
    public function addRel(AbstractRel $rel)
    {
        $this->initializeOnce();

        $this->rels[$rel->getName()] = $rel;

        return $this;
    }

    /**
     * @return AbstractRel[]
     */
    public function getRels()
    {
        $this->initializeOnce();

        return $this->rels;
    }

    /**
     * @param AbstractRel[] $rels
     */
    public function addRels(array $rels)
    {
        foreach ($rels as $rel) {
            $this->addRel($rel);
        }

        return $this;
    }

    /**
     * @param  string           $name
     * @return AbstractRel|null
     */
    public function getRel($name)
    {
        $this->initializeOnce();

        return isset($this->rels[$name]) ? $this->rels[$name] : null;
    }

    /**
     * @param  string                   $name
     * @return AbstractRel
     * @throws InvalidArgumentException If rel does not exist
     */
    public function getRelOrError($name)
    {
        $rel = $this->getRel($name);

        if ($rel === null) {
            throw new InvalidArgumentException(
                sprintf('Rel %s does not exist in %s Repo', $name, $this->getName())
            );
        }

        return $rel;
    }

    /**
     * @return EventListeners
     */
    public function getEventListeners()
    {
        $this->initializeOnce();

        return $this->eventListeners;
    }

    /**
     * @return Asserts
     */
    public function getAsserts()
    {
        $this->initializeOnce();

        return $this->asserts;
    }

    /**
     * @param  Harp\Validate\Asserts\AbstractAssert[] $asserts
     * @return AbstractRepo                           $this
     */
    public function setAsserts(array $asserts)
    {
        $this->initializeOnce();

        $this->getAsserts()->set($asserts);

        return $this;
    }

    /**
     * @param  Closure|string|array $callback
     * @return AbstractRepo         $this
     */
    public function addEventBeforeDelete($callback)
    {
        $this->getEventListeners()->addBefore(Event::DELETE, $callback);

        return $this;
    }

    /**
     * @param  Closure|string|array $callback
     * @return AbstractRepo         $this
     */
    public function addEventAfterDelete($callback)
    {
        $this->getEventListeners()->addAfter(Event::DELETE, $callback);

        return $this;
    }

    /**
     * @param  Closure|string|array $callback
     * @return AbstractRepo         $this
     */
    public function addEventBeforeSave($callback)
    {
        $this->getEventListeners()->addBefore(Event::SAVE, $callback);

        return $this;
    }

    /**
     * @param  Closure|string|array $callback
     * @return AbstractRepo         $this
     */
    public function addEventAfterSave($callback)
    {
        $this->getEventListeners()->addAfter(Event::SAVE, $callback);

        return $this;
    }

    /**
     * @param  Closure|string|array $callback
     * @return AbstractRepo         $this
     */
    public function addEventBeforeInsert($callback)
    {
        $this->getEventListeners()->addBefore(Event::INSERT, $callback);

        return $this;
    }

    /**
     * @param  Closure|string|array $callback
     * @return AbstractRepo         $this
     */
    public function addEventAfterInsert($callback)
    {
        $this->getEventListeners()->addAfter(Event::INSERT, $callback);

        return $this;
    }

    /**
     * @param  Closure|string|array $callback
     * @return AbstractRepo         $this
     */
    public function addEventBeforeUpdate($callback)
    {
        $this->getEventListeners()->addBefore(Event::UPDATE, $callback);

        return $this;
    }

    /**
     * @param  Closure|string|array $callback
     * @return AbstractRepo         $this
     */
    public function addEventAfterUpdate($callback)
    {
        $this->getEventListeners()->addAfter(Event::UPDATE, $callback);

        return $this;
    }

    /**
     * @param  Closure|string|array $callback
     * @return AbstractRepo         $this
     */
    public function addEventAfterLoad($callback)
    {
        $this->getEventListeners()->addAfter(Event::LOAD, $callback);

        return $this;
    }

    /**
     * @param  int     $event
     * @return boolean
     */
    public function hasBeforeEvent($event)
    {
        return $this->getEventListeners()->hasBeforeEvent($event);
    }

    /**
     * @param  int     $event
     * @return boolean
     */
    public function hasAfterEvent($event)
    {
        return $this->getEventListeners()->hasAfterEvent($event);
    }

    /**
     * @param  AbstractModel $model
     * @param  int           $event
     * @return AbstractRepo  $this
     */
    public function dispatchBeforeEvent($model, $event)
    {
        $this->getEventListeners()->dispatchBeforeEvent($model, $event);

        return $this;
    }

    /**
     * @param  AbstractModel $model
     * @param  int           $event
     * @return AbstractRepo  $this
     */
    public function dispatchAfterEvent($model, $event)
    {
        $this->getEventListeners()->dispatchAfterEvent($model, $event);

        return $this;
    }

    /**
     * @param  array         $fields
     * @param  int           $state
     * @return AbstractModel
     */
    public function newModel($fields = null, $state = State::PENDING)
    {
        return $this->modelReflection->newInstance($fields, $state);
    }

    /**
     * @param  array         $fields
     * @return AbstractModel
     */
    public function newVoidModel($fields = null)
    {
        return $this->modelReflection->newInstance($fields, State::VOID);
    }

    public function clear()
    {
        $this->identityMap->clear();
        $this->linkMap->clear();

        return $this;
    }

    /**
     * @param  AbstractModel $model
     * @return boolean
     */
    public function isModel(AbstractModel $model)
    {
        return $this->getRootRepo()->getModelReflection()->isInstance($model);
    }

    /**
     * @return boolean
     */
    public function getInitialized()
    {
        return $this->initialized;
    }

    public function initializeOnce()
    {
        if (! $this->initialized) {
            $this->initialized = true;
            $this->initialize();
        }
    }
}
