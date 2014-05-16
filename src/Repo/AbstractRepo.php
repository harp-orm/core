<?php

namespace CL\LunaCore\Repo;

use CL\Carpo\Asserts;
use CL\LunaCore\Model\AbstractModel;
use CL\LunaCore\Rel\AbstractRel;
use ReflectionClass;
use SplObjectStorage;
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
     * @return AbstractModel
     */
    abstract public function selectWithId($id);
    abstract public function update(SplObjectStorage $models);
    abstract public function delete(SplObjectStorage $models);
    abstract public function insert(SplObjectStorage $models);

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
     * @var Rels
     */
    private $rels;

    /**
     * @var Asserts
     */
    private $asserts;

    /**
     * @var boolean
     */
    private $initialized = false;

    /**
     * @param string $modelClass
     */
    public function __construct($modelClass)
    {
        $this->modelClass = $modelClass;
        $this->linkMap = new LinkMap();
        $this->eventListeners = new EventListeners();
        $this->asserts = new Asserts();
        $this->rels = new Rels();
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
     * @return ReflectionClass
     */
    public function getModelReflection()
    {
        return $this->modelReflection;
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
     * @return Rels
     */
    public function getRels()
    {
        $this->initializeOnce();

        return $this->rels;
    }

    /**
     * @param AbstractRel[] $rels
     */
    public function setRels(array $rels)
    {
        $this->getRels()->set($rels);

        return $this;
    }

    /**
     * @param  string           $name
     * @return AbstractRel|null
     */
    public function getRel($name)
    {
        return $this->getRels()->get($name);
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
     * @param  CL\Carpo\Asserts\AbstractAssert[] $asserts
     * @return AbstractRepo                      $this
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
        $this->getEventListeners()->addBefore(ModelEvent::DELETE, $callback);

        return $this;
    }

    /**
     * @param  Closure|string|array $callback
     * @return AbstractRepo         $this
     */
    public function addEventAfterDelete($callback)
    {
        $this->getEventListeners()->addAfter(ModelEvent::DELETE, $callback);

        return $this;
    }

    /**
     * @param  Closure|string|array $callback
     * @return AbstractRepo         $this
     */
    public function addEventBeforeSave($callback)
    {
        $this->getEventListeners()->addBefore(ModelEvent::SAVE, $callback);

        return $this;
    }

    /**
     * @param  Closure|string|array $callback
     * @return AbstractRepo         $this
     */
    public function addEventAfterSave($callback)
    {
        $this->getEventListeners()->addAfter(ModelEvent::SAVE, $callback);

        return $this;
    }

    /**
     * @param  Closure|string|array $callback
     * @return AbstractRepo         $this
     */
    public function addEventBeforeInsert($callback)
    {
        $this->getEventListeners()->addBefore(ModelEvent::INSERT, $callback);

        return $this;
    }

    /**
     * @param  Closure|string|array $callback
     * @return AbstractRepo         $this
     */
    public function addEventAfterInsert($callback)
    {
        $this->getEventListeners()->addAfter(ModelEvent::INSERT, $callback);

        return $this;
    }

    /**
     * @param  Closure|string|array $callback
     * @return AbstractRepo         $this
     */
    public function addEventBeforeUpdate($callback)
    {
        $this->getEventListeners()->addBefore(ModelEvent::UPDATE, $callback);

        return $this;
    }

    /**
     * @param  Closure|string|array $callback
     * @return AbstractRepo         $this
     */
    public function addEventAfterUpdate($callback)
    {
        $this->getEventListeners()->addAfter(ModelEvent::UPDATE, $callback);

        return $this;
    }

    /**
     * @param  Closure|string|array $callback
     * @return AbstractRepo         $this
     */
    public function addEventAfterLoad($callback)
    {
        $this->getEventListeners()->addAfter(ModelEvent::LOAD, $callback);

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
     * @param  AbstractModel[]|SplObjectStorage $models
     * @param  int                              $event
     * @return AbstractRepo                     $this
     */
    public function dispatchBeforeEvent($models, $event)
    {
        $this->getEventListeners()->dispatchBeforeEvent($models, $event);

        return $this;
    }

    /**
     * @param  AbstractModel[]|SplObjectStorage $models
     * @param  int                              $event
     * @return AbstractRepo                     $this
     */
    public function dispatchAfterEvent($models, $event)
    {
        $this->getEventListeners()->dispatchAfterEvent($models, $event);

        return $this;
    }

    /**
     * @param  array         $fields
     * @param  int           $state
     * @return AbstractModel
     */
    public function newInstance($fields = null, $state = AbstractModel::PENDING)
    {
        return $this->modelReflection->newInstance($fields, $state);
    }

    /**
     * @param  array         $fields
     * @return AbstractModel
     */
    public function newVoidInstance($fields = null)
    {
        return $this->modelReflection->newInstance($fields, AbstractModel::VOID);
    }

    /**
     * @return boolean
     */
    public function getInitialized()
    {
        return $this->initialized;
    }

    public function afterInitialize()
    {

    }

    public function initializeOnce()
    {
        if (! $this->initialized) {
            $this->initialized = true;
            $this->initialize();
            $this->afterInitialize();
        }
    }

    /**
     * @param  mixed         $id
     * @return AbstractModel
     */
    public function find($id)
    {
        $model = $this->selectWithId($id);

        return $model ? $this->getIdentityMap()->get($model) : $this->newVoidInstance();
    }

    /**
     * @return Persist
     */
    public function newPersist()
    {
        return new Persist();
    }

    /**
     * @param  AbstractModel            $model
     * @throws InvalidArgumentException If $model not the same as Repo Model
     */
    public function errorIfModelNotFromRepo(AbstractModel $model)
    {
        if (! $this->modelReflection->isInstance($model)) {
            throw new InvalidArgumentException(
                sprintf('Argument must be instance of %s', $this->modelClass)
            );
        }
    }

    /**
     * @param  AbstractRel              $rel
     * @throws InvalidArgumentException If $Link not part of this repo
     */
    public function errorIfRelNotFromRepo(AbstractRel $rel)
    {
        if (array_search($rel, $this->getRels()->all(), true) === false) {
            throw new InvalidArgumentException(
                sprintf('Rel "%s" not part of %s Repo', $rel->getName(), $this->getName())
            );
        }
    }

    /**
     * @param  AbstractModel            $model
     * @return AbstractRepo             $this
     * @throws InvalidArgumentException If $model not the same as Repo Model
     */
    public function persist(AbstractModel $model)
    {
        $this->errorIfModelNotFromRepo($model);

        $this->newPersist()
            ->add($model)
            ->execute();

        return $this;
    }

    public function addLink(AbstractModel $model, AbstractLink $link)
    {
        $this->errorIfModelNotFromRepo($model);
        $this->errorIfRelNotFromRepo($link->getRel());

        $this->linkMap->get($model)->add($link);

        return $this;
    }

    /**
     * @param  AbstractModel            $model
     * @param  string                   $name
     * @return AbstractLink
     * @throws InvalidArgumentException If $model not the same as Repo Model or no rel named $name
     */
    public function loadLink(AbstractModel $model, $name)
    {
        $this->errorIfModelNotFromRepo($model);

        $links = $this->linkMap->get($model);

        if (! $links->has($name)) {
            $this->loadRel($name, [$model]);
        }

        return $links->get($name);
    }

    /**
     * @param  string          $relName
     * @param  AbstractModel[] $models
     * @return AbstractModel[]
     */
    public function loadRel($relName, array $models)
    {
        $rel = $this->getRel($relName);

        if ($rel === null) {
            throw new InvalidArgumentException(
                sprintf('Cannot load Rel %s does not exist in %s Repo', $relName, $this->getName())
            );
        }

        return $rel->loadForeignModels($models, function (AbstractModel $model, AbstractLink $link) {
            $model->getRepo()->addLink($model, $link);
        });
    }
}
