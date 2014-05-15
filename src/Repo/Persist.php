<?php

namespace CL\LunaCore\Repo;

use SplObjectStorage;
use Closure;

use CL\LunaCore\Rel\DeleteInterface;
use CL\LunaCore\Rel\InsertInterface;
use CL\LunaCore\Rel\UpdateInterface;
use CL\LunaCore\Model\AbstractModel;
use CL\LunaCore\Util\Objects;


/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Persist
{
    private $models;

    public function __construct()
    {
        $this->models = new SplObjectStorage();
    }

    public function all()
    {
        return $this->models;
    }

    public function getDeleted()
    {
        return Objects::filter($this->models, function ($model) {
            return $model->isDeleted();
        });
    }

    public function getPending()
    {
        return Objects::filter($this->models, function ($model) {
            return $model->isPending();
        });
    }

    public function getChanged()
    {
        return Objects::filter($this->models, function ($model) {
            return ($model->isChanged() AND $model->isPersisted());
        });
    }

    public function addModel(AbstractModel $model)
    {
        $this->models->attach($model);

        return $this;
    }

    public function add(AbstractModel $model)
    {
        if (! $this->models->contains($model)) {
            $this->addModel($model);

            $modelLinks = $model->getRepo()->getLinkMap()->get($model);

            foreach ($modelLinks->getModels() as $linkedModel) {
                $this->add($linkedModel);
            }
        }

        return $this;
    }

    /**
     * @param SplObjectStorage|AbstractModel[] $models
     */
    public function set($models)
    {
        foreach ($models as $model) {
            $this->add($model);
        }

        return $this;
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

        return $this;
    }

    public function addFromDeleteRels()
    {
        return $this
            ->eachLink(function (AbstractModel $model, AbstractLink $link) {
                if ($link->getRel() instanceof DeleteInterface) {
                    $this->set($link->getRel()->delete($model, $link));
                }
            });
    }

    public function addFromInsertRels()
    {
        return $this
            ->eachLink(function (AbstractModel $model, AbstractLink $link) {
                if ($link->getRel() instanceof InsertInterface) {
                    $this->set($link->getRel()->insert($model, $link));
                }
            });
    }

    public function callUpdateRels()
    {
        return $this
            ->eachLink(function (AbstractModel $model, AbstractLink $link) {
                if ($link->getRel() instanceof UpdateInterface) {
                    $link->getRel()->update($model, $link);
                }
            });
    }


    public static function groupByRepo(SplObjectStorage $models)
    {
        return Objects::groupBy($models, function($model) {
            return $model->getRepo();
        });
    }

    public function execute()
    {
        $this->addFromDeleteRels();

        self::persist($this->getDeleted(), [ModelEvent::DELETE], function (AbstractRepo $repo, SplObjectStorage $models) {
            $repo->delete($models);
        });

        $this->addFromInsertRels();

        self::persist($this->getPending(), [ModelEvent::INSERT, ModelEvent::SAVE], function (AbstractRepo $repo, SplObjectStorage $models) {
            $repo->insert($models);
        });

        $this->callUpdateRels();

        self::persist($this->getChanged(), [ModelEvent::UPDATE, ModelEvent::SAVE], function (AbstractRepo $repo, SplObjectStorage $models) {
            $repo->update($models);
        });
    }

    public static function persist(SplObjectStorage $models, array $events, Closure $yield)
    {
        $groups = self::groupByRepo($models);

        foreach ($groups as $repo) {
            foreach ($events as $event) {
                $repo->dispatchBeforeEvent($models, $event);
            }

            $yield($repo, $groups->getInfo());

            foreach ($events as $event) {
                $repo->dispatchAfterEvent($models, $event);
            }
        }
    }
}
