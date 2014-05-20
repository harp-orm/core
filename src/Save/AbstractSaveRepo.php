<?php

namespace CL\LunaCore\Save;

use CL\LunaCore\Model\AbstractModel;
use CL\LunaCore\Model\State;
use CL\LunaCore\Model\Models;
use CL\LunaCore\Repo\AbstractRepo;
use CL\LunaCore\Repo\AbstractLink;
use CL\LunaCore\Repo\Event;
use CL\Util\Arr;
use InvalidArgumentException;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractSaveRepo extends AbstractRepo
{
    /**
     * @return AbstractFind
     */
    abstract public function findAll();

    /**
     * @param  Models $models
     * @return AbstractSaveRepo $this
     */
    abstract public function update(Models $models);

    /**
     * @param  Models $models
     * @return AbstractSaveRepo $this
     */
    abstract public function delete(Models $models);

    /**
     * @param  Models $models
     * @return AbstractSaveRepo $this
     */
    abstract public function insert(Models $models);

    /**
     * @param  mixed         $id
     * @return AbstractModel
     */
    public function find($id, $flags = null)
    {
        $models = $this
            ->findAll()
            ->whereKey($id)
            ->limit(1)
            ->loadRaw($flags);

        $model = reset($models);

        return $model ? $this->getIdentityMap()->get($model) : $this->newVoidInstance();
    }

    /**
     * @return Save
     */
    public function newSave()
    {
        return new Save();
    }

    /**
     * @param  AbstractModel            $model
     * @return AbstractSaveRepo         $this
     * @throws InvalidArgumentException If $model does not belong to repo
     */
    public function save(AbstractModel $model)
    {
        if (! $this->isModel($model)) {
            throw new InvalidArgumentException(
                sprintf('Model must be %s, was %s', $this->getModelClass(), get_class($model))
            );
        }

        $this->newSave()
            ->add($model)
            ->execute();

        return $this;
    }

    /**
     * @param  AbstractModel    $model
     * @param  AbstractLink     $link
     * @return AbstractSaveRepo $this
     * @throws InvalidArgumentException If $model does not belong to repo
     */
    public function addLink(AbstractModel $model, AbstractLink $link)
    {
        $this->getLinkMap()->get($model)->add($link);

        return $this;
    }

    /**
     * @param  AbstractModel  $model
     * @param  string         $name
     * @return AbstractLink
     * @throws InvalidArgumentException If $model does not belong to repo
     */
    public function loadLink(AbstractModel $model, $name, $flags = null)
    {
        $links = $this->getLinkMap()->get($model);

        if (! $links->has($name)) {
            $models = new Models();
            $models->add($model);
            $this->loadRelFor($models, $name, $flags);
        }

        return $links->get($name);
    }

    /**
     * @param  Models          $models
     * @param  string          $relName
     * @return Models
     * @throws InvalidArgumentException If $relName does not belong to repo
     */
    public function loadRelFor(Models $models, $relName, $flags = null)
    {
        $rel = $this->getRelOrError($relName);

        $foreign = $rel->loadForeignModels($models, $flags);

        foreach ($rel->linkModels($models, $foreign) as $model => $link) {
            $model->getRepo()->addLink($model, $link);
        }

        return $foreign;
    }

    /**
     * @param  Models $models
     * @param  array  $rels
     * @param  int    $state
     * @return AbstractSaveRepo $this
     */
    public function loadAllRelsFor(Models $models, array $rels, $state = null)
    {
        $rels = Arr::toAssoc($rels);

        foreach ($rels as $relName => $childRels) {
            $foreign = $this->loadRelFor($models, $relName, $state);

            if ($childRels) {
                $rel = $this->getRel($relName);
                $rel->getForeignRepo()->loadAllRelsFor($foreign, $childRels, $state);
            }
        }

        return $this;
    }

    /**
     * @param  Models $models
     * @return AbstractSaveRepo $this
     */
    public function updateModels(Models $models)
    {
        foreach ($models as $model) {
            if ($model->isSoftDeleted()) {
                $this->dispatchBeforeEvent($model, Event::DELETE);
            } else {
                $this->dispatchBeforeEvent($model, Event::UPDATE);
                $this->dispatchBeforeEvent($model, Event::SAVE);
            }
        }

        $this->update($models);

        foreach ($models as $model) {
            if ($model->isSoftDeleted()) {
                $this->dispatchAfterEvent($model, Event::DELETE);
            } else {
                $this->dispatchAfterEvent($model, Event::UPDATE);
                $this->dispatchAfterEvent($model, Event::SAVE);
            }
        }

        return $this;
    }

    /**
     * @param  Models $models
     * @return AbstractSaveRepo $this
     */
    public function deleteModels(Models $models)
    {
        foreach ($models as $model) {
            $this->dispatchBeforeEvent($model, Event::DELETE);
        }

        $this->delete($models);

        foreach ($models as $model) {
            $this->dispatchAfterEvent($model, Event::DELETE);
        }

        return $this;
    }

    /**
     * @param  Models $models
     * @return AbstractSaveRepo $this
     */
    public function insertModels(Models $models)
    {
        foreach ($models as $model) {
            $this->dispatchBeforeEvent($model, Event::INSERT);
            $this->dispatchBeforeEvent($model, Event::SAVE);
        }

        $this->insert($models);

        foreach ($models as $model) {
            $model
                ->resetOriginals()
                ->setState(State::SAVED);

            $this->dispatchAfterEvent($model, Event::INSERT);
            $this->dispatchAfterEvent($model, Event::SAVE);
        }

        return $this;
    }
}
