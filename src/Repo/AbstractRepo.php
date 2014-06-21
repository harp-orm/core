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

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractRepo implements RepoInterface
{
    private static $instances;

    public static function get()
    {
        $class = get_called_class();

        if (! isset(self::$instances[$class])) {
            self::$instances[$class] = static::newInstance();
        }

        return self::$instances[$class];
    }

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

    /**
     * @param string $modelClass
     */
    public function __construct($modelClass)
    {
        $this->modelClass = $modelClass;
        $this->linkMap = new LinkMap($this);
        $this->eventListeners = new EventListeners();
        $this->serializers = new Serializers();
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
     * @param  AbstractModel            $name
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
     * @param  Harp\Validate\Asserts\AbstractAssert[] $asserts
     * @return AbstractRepo                           $this
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
     * @param  Harp\Serializer\AbstractSerializer[] $serializers
     * @return AbstractRepo                         $this
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
