<?php

namespace CL\LunaCore\Repo;

use ReflectionClass;
use SplObjectStorage;
use InvalidArgumentException;
use CL\Carpo\Asserts;
use CL\LunaCore\Model\AbstractModel;
use CL\LunaCore\Rel\AbstractRel;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractRepo
{
    abstract public function selectWithId($id);
    abstract public function update(SplObjectStorage $models);
    abstract public function delete(SplObjectStorage $models);
    abstract public function insert(SplObjectStorage $models);

    private $modelClass;
    private $identityMap;
    private $linkMap;
    private $modelReflection;
    private $primaryKey = 'id';
    private $rels;
    private $initialized;

    function __construct($modelClass)
    {
        $this->modelClass = $modelClass;
        $this->linkMap = new LinkMap();
        $this->eventListeners = new EventListeners();
        $this->asserts = new Asserts();
        $this->rels = new Rels();
        $this->modelReflection = new ReflectionClass($modelClass);
        $this->identityMap = new IdentityMap($this->modelReflection);
    }

    public function getName()
    {
        return $this->modelReflection->getShortName();
    }

    public function getIdentityMap()
    {
        return $this->identityMap;
    }

    public function getLinkMap()
    {
        return $this->linkMap;
    }

    public function getModelClass()
    {
        return $this->modelClass;
    }

    public function getModelReflection()
    {
        return $this->modelReflection;
    }

    public function getPrimaryKey()
    {
        $this->initializeAllOnce();

        return $this->primaryKey;
    }

    public function setPrimaryKey($primaryKey)
    {
        $this->primaryKey = $primaryKey;

        return $this;
    }

    public function getRels()
    {
        $this->initializeAllOnce();

        return $this->rels;
    }

    public function setRels(array $rels)
    {
        $this->getRels()->set($rels);

        return $this;
    }

    public function getRel($name)
    {
        return $this->getRels()->get($name);
    }

    public function getEventListeners()
    {
        $this->initializeAllOnce();

        return $this->eventListeners;
    }

    public function getAsserts()
    {
        $this->initializeAllOnce();

        return $this->asserts;
    }

    public function setAsserts(array $asserts)
    {
        $this->initializeAllOnce();

        $this->getAsserts()->set($asserts);

        return $this;
    }

    public function setEventBeforeDelete($callback)
    {
        $this->getEventListeners()->addBefore(AbstractModel::DELETE, $callback);

        return $this;
    }

    public function setEventAfterDelete($callback)
    {
        $this->getEventListeners()->addAfter(NodeEvent::DELETE, $callback);

        return $this;
    }

    public function setEventBeforeSave($callback)
    {
        $this->getEventListeners()->addBefore(NodeEvent::SAVE, $callback);

        return $this;
    }

    public function setEventAfterSave($callback)
    {
        $this->getEventListeners()->addAfter(NodeEvent::SAVE, $callback);

        return $this;
    }

    public function setEventBeforeInsert($callback)
    {
        $this->getEventListeners()->addBefore(NodeEvent::INSERT, $callback);

        return $this;
    }

    public function setEventAfterInsert($callback)
    {
        $this->getEventListeners()->addAfter(NodeEvent::INSERT, $callback);

        return $this;
    }

    public function setEventBeforeUpdate($callback)
    {
        $this->getEventListeners()->addBefore(NodeEvent::UPDATE, $callback);

        return $this;
    }

    public function setEventAfterUpdate($callback)
    {
        $this->getEventListeners()->addAfter(NodeEvent::UPDATE, $callback);

        return $this;
    }

    public function setEventAfterLoad($callback)
    {
        $this->getEventListeners()->addAfter(NodeEvent::LOAD, $callback);

        return $this;
    }

    public function dispatchEvent($event, Model $target)
    {
        return $this->getEventListeners()->dispatchEvent($event, $target);
    }

    public function hasEvent($event)
    {
        return $this->getEventListeners()->hasEvent($event);
    }

    public function dispatchBeforeEvent($models, $event)
    {
        $this->getEventListeners()->dispatchBeforeEvent($models, $event);
    }

    public function dispatchAfterEvent($models, $event)
    {
        $this->getEventListeners()->dispatchAfterEvent($models, $event);
    }

    public function newInstance($fields = null, $state = AbstractModel::PENDING)
    {
        return $this->modelReflection->newInstance($fields, $state);
    }

    public function newVoidInstance($fields = null)
    {
        return $this->modelReflection->newInstance($fields, AbstractModel::VOID);
    }

    public function initializeAll()
    {
        $this->initialize();

        foreach ((new ReflectionClass($this))->getTraits() as $trait) {
            if ($trait->hasMethod('initializeTrait')) {
                $trait->getMethod('initializeTrait')->invoke(null, $this);
            }
        }
    }

    public function initializeAllOnce()
    {
        if (! $this->initialized)
        {
            $this->initialized = true;
            $this->initializeAll();
        }
    }

    public function find($id)
    {
        $model = $this->selectWithId($id);

        return $model ? $this->getIdentityMap()->get($model) : $this->newVoidInstance();
    }

    public function persist(AbstractModel $model)
    {
        if (! $this->modelReflection->isInstance($model)) {
            throw new InvalidArgumentException(sprintf('Argument must be instance of %s', $this->modelClass));
        }

        $queue = new Persist();
        $queue
            ->add($model)
            ->execute();
    }

    public function loadLink(AbstractModel $model, $linkName)
    {
        if (! $this->modelReflection->isInstance($model)) {
            throw new InvalidArgumentException(sprintf('Argument must be instance of %s', $this->modelClass));
        }

        $links = $this->linkMap->get($model);

        if (! $links->has($linkName)) {
            $rel = $this->getRel($linkName);

            $this->loadRel($rel, [$model]);
        }

        return $links->get($linkName);
    }

    public function loadRel(AbstractRel $rel, array $models)
    {
        $foreign = $rel->loadForeignForNodes($models);

        $linked = $rel->linkToForeign($models, $foreign);

        foreach ($models as $model) {
            if ($linked->contains($model)) {
                $link = $rel->newLink($linked[$model]);
            } else {
                $link = $rel->newEmptyLink();
            }

            $this->linkMap->get($model)->add($rel->getName(), $link);
        }

        return $foreign;
    }
}
