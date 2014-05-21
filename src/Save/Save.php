<?php

namespace CL\LunaCore\Save;

use CL\LunaCore\Rel\DeleteInterface;
use CL\LunaCore\Rel\InsertInterface;
use CL\LunaCore\Rel\UpdateInterface;
use CL\LunaCore\Model\AbstractModel;
use CL\LunaCore\Save\AbstractSaveRepo;
use CL\LunaCore\Repo\AbstractLink;
use CL\LunaCore\Model\Models;
use SplObjectStorage;
use Closure;

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

    public function eachLink(Closure $yield)
    {
        foreach ($this as $model) {
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
            $rel = $link->getRel();
            if ($rel instanceof DeleteInterface) {
                $this->addArray($rel->delete($model, $link));
            }
        });
    }

    public function addFromInsertRels()
    {
        $this->eachLink(function (AbstractModel $model, AbstractLink $link) {
            $rel = $link->getRel();
            if ($rel instanceof InsertInterface) {
                $this->addArray($rel->insert($model, $link));
            }
        });
    }

    public function callUpdateRels()
    {
        $this->eachLink(function (AbstractModel $model, AbstractLink $link) {
            $rel = $link->getRel();
            if ($rel instanceof UpdateInterface) {
                $rel->update($model, $link);
            }
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
