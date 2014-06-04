<?php

namespace Harp\Core\Save;

use Harp\Core\Model\AbstractModel;
use Harp\Core\Model\Models;
use Harp\Core\Repo\AbstractLink;
use Closure;
use Countable;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Save implements Countable
{
    /**
     * @var Models
     */
    private $models;

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
     * @param  AbstractModel[] $models
     * @return Save            $this
     */
    public function addArray(array $models)
    {
        foreach ($models as $model) {
            $this->add($model);
        }

        return $this;
    }

    /**
     * @param  Models $models
     * @return Save   $this
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
                    if (($new = $yield($link))) {
                        $this->addAll($new);
                    }
                }
            }
        }
    }

    public function addFromDeleteRels()
    {
        $this->eachLink(function (AbstractLink $link) {
            return $link->delete();
        });
    }

    public function addFromInsertRels()
    {
        $this->eachLink(function (AbstractLink $link) {
            return $link->insert();
        });
    }

    public function addFromUpdateRels()
    {
        $this->eachLink(function (AbstractLink $link) {
            return $link->update();
        });
    }

    public function execute()
    {
        $this->addFromDeleteRels();

        $this->getModelsToDelete()->byRepo(function (AbstractSaveRepo $repo, Models $models) {
            $repo->deleteModels($models);
        });

        $this->addFromInsertRels();

        $this->getModelsToInsert()->assertValid()->byRepo(function (AbstractSaveRepo $repo, Models $models) {
            $repo->insertModels($models);
        });

        $this->addFromUpdateRels();

        $this->getModelsToUpdate()->assertValid()->byRepo(function (AbstractSaveRepo $repo, Models $models) {
            $repo->updateModels($models);
        });
    }
}
