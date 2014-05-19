<?php

namespace CL\LunaCore\Save;

use CL\LunaCore\Model\AbstractModel;
use CL\LunaCore\Model\State;
use CL\LunaCore\Model\Models;
use CL\LunaCore\Repo\AbstractRepo;
use CL\LunaCore\Repo\AbstractLink;
use CL\LunaCore\Repo\Event;
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
     * @throws InvalidArgumentException If $model not the same as Repo Model
     */
    public function errorIfModelNotFromRepo(AbstractModel $model)
    {
        if (! $this->getModelReflection()->isInstance($model)) {
            throw new InvalidArgumentException(
                sprintf('Argument must be instance of %s', $this->modelClass)
            );
        }
    }

    /**
     * @param  AbstractModel            $model
     * @return AbstractRepo             $this
     * @throws InvalidArgumentException If $model not the same as Repo Model
     */
    public function save(AbstractModel $model)
    {
        $this->errorIfModelNotFromRepo($model);

        $this->newSave()
            ->add($model)
            ->execute();

        return $this;
    }

    public function addLink(AbstractModel $model, AbstractLink $link)
    {
        $this->errorIfModelNotFromRepo($model);

        $this->getLinkMap()->get($model)->add($link);

        return $this;
    }

    /**
     * @param  AbstractModel            $model
     * @param  string                   $name
     * @return AbstractLink
     * @throws InvalidArgumentException If $model not the same as Repo Model
     */
    public function loadLink(AbstractModel $model, $name, $flags = null)
    {
        $this->errorIfModelNotFromRepo($model);

        $links = $this->getLinkMap()->get($model);

        if (! $links->has($name)) {
            $models = new Models();
            $models->add($model);
            $this->loadRel($name, $models, $flags);
        }

        return $links->get($name);
    }

    /**
     * @param  string          $relName
     * @param  Models          $models
     * @return Models
     */
    public function loadRel($relName, Models $models, $flags = null)
    {
        $rel = $this->getRel($relName);

        if ($rel === null) {
            throw new InvalidArgumentException(
                sprintf('Cannot load Rel %s does not exist in %s Repo', $relName, $this->getName())
            );
        }

        $foreign = $rel->loadForeignModels($models, $flags);

        foreach ($rel->linkModels($models, $foreign) as $model => $link) {
            $model->getRepo()->addLink($model, $link);
        }

        return $foreign;
    }

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

            $this->dispatchBeforeEvent($model, Event::SAVE);
            $this->dispatchBeforeEvent($model, Event::INSERT);
        }

        return $this;
    }


}
