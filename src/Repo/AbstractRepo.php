<?php

namespace Harp\Core\Repo;

use Harp\Validate\Asserts;
use Harp\Serializer\Serializers;
use Harp\Core\Model\AbstractModel;
use Harp\Core\Model\State;
use Harp\Core\Rel\AbstractRel;
use ReflectionClass;
use LogicException;
use InvalidArgumentException;

/**
 * A Repo represents a storage and configuration medium for models. Each model has a corresponding "repo".
 * Repos are also singleton classes. You can get the repo object with the "get" static method
 *
 * This class is the core implementation of a repo and contins all the logic for the "configuration" part.
 *
 * The abstract method "initialize" which is implemented in your own repos is called only once. It is
 * distinct from the __construct, becase it can create a lot of overhead. Since relations require "repo"
 * requesting a single "repo" could trigger the constructors of all the other repos, associated with it,
 * and their related repo's too. Thats why we need "initialize" method, which will lazy load all the relations.
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
abstract class AbstractRepo
{
    /**
     * @var string
     */
    private $name;

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
     * @var Serializers
     */
    private $serializers;

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

    public function __construct($class)
    {
        $this->modelClass = $class;
        $this->modelReflection = new ReflectionClass($class);
        $this->eventListeners = new EventListeners();
        $this->serializers = new Serializers();
        $this->asserts = new Asserts();
        $this->identityMap = new IdentityMap($this);
        $this->linkMap = new LinkMap($this);

        $class = explode('\\', $class);
        $this->name = end($class);
    }

    /**
     * @return string
     */
    public function getName()
    {
        $this->initializeOnce();

        return $this->name;
    }

    /**
     * @return IdentityMap
     */
    public function getIdentityMap()
    {
        $this->initializeOnce();

        return $this->identityMap;
    }

    /**
     * @return LinkMap
     */
    public function getLinkMap()
    {
        $this->initializeOnce();

        return $this->linkMap;
    }

    /**
     * @return string
     */
    public function getModelClass()
    {
        $this->initializeOnce();

        return $this->modelClass;
    }

    /**
     * @param  string $modelClass
     * @return AbstractRepo $this
     */
    public function setModelClass($modelClass)
    {
        $this->modelClass = $modelClass;

        return $this;
    }

    /**
     * Used for inherited repos.
     *
     * @return AbstractRepo
     */
    public function getRootRepo()
    {
        $this->initializeOnce();

        return $this->rootRepo ?: $this;
    }

    /**
     * Used for inherited repos. All the child repos should call this, to set the parent repo object explicitly.
     *
     * @param  AbstractRepo $rootRepo
     * @return AbstractRepo $this
     */
    public function setRootRepo(AbstractRepo $rootRepo)
    {
        if (! $rootRepo->getInherited()) {
            throw new LogicException('The root repo must be set as inherited (->setInherited(true))');
        }

        if (! $this->inherited) {
            throw new LogicException('You must call parent::initialize() for inherited repos');
        }

        $this->rootRepo = $rootRepo;

        return $this;
    }

    /**
     * @return ReflectionClass
     */
    public function getModelReflection()
    {
        $this->initializeOnce();

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
     * Enables "soft delete" on models of this repo.
     * You will need to add the SoftDeleteTrait to the model class too.
     *
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
     * Enables Repo "inheritance" allowing multiple repos to share one storage table
     * You will need to call setRootRepo on all the child repos.
     *
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
     * Check if a model belongs to this repo. Child classes are also accepted
     *
     * @param  AbstractModel            $model
     * @throws InvalidArgumentException If model not part of repo
     */
    public function assertModel(AbstractModel $model)
    {
        if (! $this->isModel($model)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Model must be instance of %s, but was %s',
                    $this->getRootRepo()->getModelClass(),
                    get_class($model)
                )
            );
        }
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
     * @param  \Harp\Validate\Asserts\AbstractAssert[] $asserts
     * @return AbstractRepo                            $this
     */
    public function addAsserts(array $asserts)
    {
        $this->initializeOnce();

        $this->getAsserts()->set($asserts);

        return $this;
    }

    /**
     * @return Serializers
     */
    public function getSerializers()
    {
        $this->initializeOnce();

        return $this->serializers;
    }

    /**
     * @param  \Harp\Serializer\AbstractSerializer[] $serializers
     * @return AbstractRepo                          $this
     */
    public function addSerializers(array $serializers)
    {
        $this->initializeOnce();

        $this->getSerializers()->set($serializers);

        return $this;
    }

    /**
     * @param  integer              $event
     * @param  Closure|string|array $callback
     * @return AbstractRepo         $this
     */
    public function addEventBefore($event, $callback)
    {
        $this->getEventListeners()->addBefore($event, $callback);

        return $this;
    }

    /**
     * @param  integer              $event
     * @param  Closure|string|array $callback
     * @return AbstractRepo         $this
     */
    public function addEventAfter($event, $callback)
    {
        $this->getEventListeners()->addAfter($event, $callback);

        return $this;
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
        return $this->getModelReflection()->newInstance($fields, $state);
    }

    /**
     * @param  array         $fields
     * @return AbstractModel
     */
    public function newVoidModel($fields = null)
    {
        return $this->getModelReflection()->newInstance($fields, State::VOID);
    }

    /**
     * @param  AbstractModel $model
     * @return AbstractRepo $this
     */
    public function initializeModel(AbstractModel $model)
    {
        $this->initializeOnce();

        $this->serializers->unserialize($model);

        if ($this->inherited) {
            $model->class = $this->modelClass;
        }

        $this->dispatchAfterEvent($model, Event::CONSTRUCT);
    }

    /**
     * Clear IdentityMap and LinkMap
     *
     * @return AbstractRepo $this
     */
    public function clear()
    {
        $this->initializeOnce();

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

    /**
     * Call "initialize" method only once,
     * this is determined by the "initialized" property.
     */
    public function initializeOnce()
    {
        if (! $this->initialized) {
            $this->initialized = true;

            if ($this->modelReflection->hasMethod('initialize')) {
                $this->modelReflection->getMethod('initialize')->invoke(null, $this);
            }
        }
    }
}
