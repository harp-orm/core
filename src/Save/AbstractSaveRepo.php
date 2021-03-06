<?php

namespace Harp\Core\Save;

use Harp\Core\Model\AbstractModel;
use Harp\Core\Model\State;
use Harp\Core\Model\Models;
use Harp\Core\Repo\AbstractRepo;
use Harp\Core\Repo\AbstractLink;
use Harp\Core\Repo\Event;
use Harp\Util\Arr;
use InvalidArgumentException;

/**
 * This is the second part of the repo core, and handles loading and saving models.
 * It implements eager loading and handles model life-cycle event dispatching.
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
abstract class AbstractSaveRepo extends AbstractRepo
{
    /**
     * @param  Models           $models
     * @return AbstractSaveRepo $this
     */
    abstract public function update(Models $models);

    /**
     * @param  Models           $models
     * @return AbstractSaveRepo $this
     */
    abstract public function delete(Models $models);

    /**
     * @param  Models           $models
     * @return AbstractSaveRepo $this
     */
    abstract public function insert(Models $models);

    public function findAll()
    {
        $class = $this->getModelClass();

        return $class::findAll();
    }

    /**
     * Add an already loaded link. Used in eager loading.
     *
     * @param  AbstractLink             $link
     * @return AbstractSaveRepo         $this
     * @throws InvalidArgumentException If $model does not belong to repo
     */
    public function addLink(AbstractLink $link)
    {
        $this->getLinkMap()->addLink($link);

        return $this;
    }

    /**
     * @param  AbstractModel            $model
     * @param  string                   $name
     * @return AbstractLink
     * @throws InvalidArgumentException If $model does not belong to repo
     */
    public function loadLink(AbstractModel $model, $name, $flags = null)
    {
        $links = $this->getLinkMap()->get($model);

        if (! $links->has($name)) {
            $this->loadRelFor(new Models([$model]), $name, $flags);
        }

        return $links->get($name);
    }

    /**
     * Load models for a given relation.
     *
     * @param  Models                   $models
     * @param  string                   $relName
     * @return Models
     * @throws InvalidArgumentException If $relName does not belong to repo
     */
    public function loadRelFor(Models $models, $relName, $flags = null)
    {
        $rel = $this->getRelOrError($relName);

        $foreign = $rel->loadForeignModels($models, $flags);

        $rel->linkModels($models, $foreign, function (AbstractLink $link) {
            $link->getModel()->getRepo()->addLink($link);
        });

        return $foreign;
    }

    /**
     * Load all the models for the provided relations. This is the meat of the eager loading
     *
     * @param  Models           $models
     * @param  array            $rels
     * @param  int              $flags
     * @return AbstractSaveRepo $this
     */
    public function loadAllRelsFor(Models $models, array $rels, $flags = null)
    {
        $rels = Arr::toAssoc($rels);

        foreach ($rels as $relName => $childRels) {
            $foreign = $this->loadRelFor($models, $relName, $flags);

            if ($childRels) {
                $rel = $this->getRel($relName);
                $rel->getForeignRepo()->loadAllRelsFor($foreign, $childRels, $flags);
            }
        }

        return $this;
    }

    /**
     * Call all the events associated with model updates. Perform the update itself.
     *
     * @param  Models           $models
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

            $model->resetOriginals();

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
     * Call all the events associated with model deletion. Perform the deletion itself.
     *
     * @param  Models           $models
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
     * Call all the events associated with model insertion. Perform the insertion itself.
     *
     * @param  Models           $models
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
