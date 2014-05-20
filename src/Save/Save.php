<?php

namespace CL\LunaCore\Save;

use CL\LunaCore\Rel\DeleteInterface;
use CL\LunaCore\Rel\InsertInterface;
use CL\LunaCore\Rel\UpdateInterface;
use CL\LunaCore\Model\AbstractModel;
use CL\LunaCore\Model\Models;
use SplObjectStorage;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Save extends Models
{
    public static function fromObjects(SplObjectStorage $array)
    {
        $save = new Save();
        return $save->addObjects($array);
    }

    /**
     * @return Models
     */
    public function getModelsToDelete()
    {
        return $this->filter(function (AbstractModel $model) {
            return ($model->isDeleted() and ! $model->isSoftDeleted());
        });
    }

    /**
     * @return Models
     */
    public function getModelsToInsert()
    {
        return $this->filter(function (AbstractModel $model) {
            return $model->isPending();
        });
    }

    /**
     * @return Models
     */
    public function getModelsToUpdate()
    {
        return $this->filter(function (AbstractModel $model) {
            return ($model->isChanged() and ($model->isSaved() or $model->isSoftDeleted()));
        });
    }

    public function addModel(AbstractModel $model)
    {
        parent::add($model);

        return $this;
    }

    public function add(AbstractModel $model)
    {
        if (! $this->has($model)) {
            $this->addModel($model);

            $modelLinks = $model->getRepo()->getLinkMap()->get($model);

            foreach ($modelLinks->getModels() as $linkedModel) {
                $this->add($linkedModel);
            }
        }

        return $this;
    }

    public function eachLink()
    {
        foreach ($this as $model) {
            $linkMap = $model->getRepo()->getLinkMap();

            if ($linkMap->has($model)) {
                $links = $linkMap->get($model)->all();

                foreach ($links as $link) {
                    yield $model => $link;
                }
            }
        }
    }

    public function addFromDeleteRels()
    {
        foreach ($this->eachLink() as $model => $link) {
            $rel = $link->getRel();
            if ($rel instanceof DeleteInterface) {
                $this->addArray($rel->delete($model, $link));
            }
        }
    }

    public function addFromInsertRels()
    {
        foreach ($this->eachLink() as $model => $link) {
            $rel = $link->getRel();
            if ($rel instanceof InsertInterface) {
                $this->addArray($rel->insert($model, $link));
            }
        }
    }

    public function callUpdateRels()
    {
        foreach ($this->eachLink() as $model => $link) {
            $rel = $link->getRel();
            if ($rel instanceof UpdateInterface) {
                $rel->update($model, $link);
            }
        }
    }

    public function execute()
    {
        $this->addFromDeleteRels();

        foreach ($this->getModelsToDelete()->byRepo() as $repo => $models) {
            $repo->deleteModels($models);
        }

        $this->addFromInsertRels();

        foreach ($this->getModelsToInsert()->byRepo() as $repo => $models) {
            $repo->insertModels($models);
        }

        $this->callUpdateRels();

        foreach ($this->getModelsToUpdate()->byRepo() as $repo => $models) {
            $repo->updateModels($models);
        }
    }
}
