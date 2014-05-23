<?php

namespace CL\LunaCore\Save;

use CL\LunaCore\Model\AbstractModel;
use CL\LunaCore\Model\Models;
use CL\LunaCore\Repo\AbstractLink;
use Closure;
use Countable;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Save implements Countable
{
    protected $models;

    public function __construct(array $models = array())
    {
        $this->models = new Models();

        $this->addArray($models);
    }

    /**
     * @return Models
     */
    public function getModelsToDelete()
    {
        return $this->models->filter(function (AbstractModel $model) {
            return ($model->isDeleted() and ! $model->isSoftDeleted());
        });
    }

    /**
     * @return Models
     */
    public function getModelsToInsert()
    {
        return $this->models->filter(function (AbstractModel $model) {
            return $model->isPending();
        });
    }

    /**
     * @return Models
     */
    public function getModelsToUpdate()
    {
        return $this->models->filter(function (AbstractModel $model) {
            return ($model->isChanged() and ($model->isSaved() or $model->isSoftDeleted()));
        });
    }

    public function addShallow(AbstractModel $model)
    {
        $this->models->add($model);

        return $this;
    }

    public function add(AbstractModel $model)
    {
        if (! $this->has($model)) {
            $this->addShallow($model);

            $modelLinks = $model->getRepo()->getLinkMap()->get($model);

            foreach ($modelLinks->getModels() as $linkedModel) {
                $this->add($linkedModel);
            }
        }

        return $this;
    }

    /**
     * @param AbstractModel[] $models
     * @return Save $this
     */
    public function addArray(array $models)
    {
        foreach ($models as $model) {
            $this->add($model);
        }

        return $this;
    }

    /**
     * @param Models $models
     * @return Save $this
     */
    public function addAll(Models $models)
    {
        foreach ($models as $model) {
            $this->add($model);
        }

        return $this;
    }

    /**
     * @param  AbstractModel $model
     * @return boolean
     */
    public function has(AbstractModel $model)
    {
        return $this->models->has($model);
    }

    public function clear()
    {
        $this->models->clear();

        return $this;
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->models->count();
    }

    public function eachLink(Closure $yield)
    {
        foreach ($this->models as $model) {
            $linkMap = $model->getRepo()->getLinkMap();

            if ($linkMap->has($model)) {
                $links = $linkMap->get($model)->all();

                foreach ($links as $link) {
                    $yield($model, $link);
                }
            }
        }
    }

    public function addFromDeleteRels()
    {
        $this->eachLink(function (AbstractModel $model, AbstractLink $link) {
            $this->addAll($link->delete($model));
        });
    }

    public function addFromInsertRels()
    {
        $this->eachLink(function (AbstractModel $model, AbstractLink $link) {
            $this->addAll($link->insert($model));
        });
    }

    public function callUpdateRels()
    {
        $this->eachLink(function (AbstractModel $model, AbstractLink $link) {
            $link->update($model);
        });
    }

    public function execute()
    {
        $this->addFromDeleteRels();

        $this->getModelsToDelete()->byRepo(function (AbstractSaveRepo $repo, Models $models) {
            $repo->deleteModels($models);
        });

        $this->addFromInsertRels();

        $this->getModelsToInsert()->byRepo(function (AbstractSaveRepo $repo, Models $models) {
            $repo->insertModels($models);
        });

        $this->callUpdateRels();

        $this->getModelsToUpdate()->byRepo(function (AbstractSaveRepo $repo, Models $models) {
            $repo->updateModels($models);
        });
    }
}
