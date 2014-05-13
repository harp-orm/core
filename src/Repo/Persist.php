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
class Persist extends SplObjectStorage
{
    public function getDeleted()
    {
        return Objects::filter($this, function ($model) {
            return $model->isDeleted();
        });
    }

    public function getPending()
    {
        return Objects::filter($this, function ($model) {
            return $model->isPending();
        });
    }

    public function getChanged()
    {
        return Objects::filter($this, function ($model) {
            return ($model->isChanged() AND $model->isPersisted());
        });
    }

    public function addOnly(AbstractModel $model)
    {
        $this->attach($model);

        return $this;
    }

    public function add(AbstractModel $model)
    {
        if (! $this->contains($model)) {
            $this->addOnly($model);

            $modelLinks = $model->getRepo()->getLinkMap()->get($model);
            foreach ($modelLinks->getNodes() as $linkedNode) {
                $this->addOnly($linkedNode);
            }
        }

        return $this;
    }

    public function set(SplObjectStorage $models)
    {
        foreach ($models as $model) {
            $this->add($model);
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

        return $this;
    }

    public function addDeletedLinks()
    {
        return $this
            ->eachLink(function (AbstractModel $model, AbstractLink $link) {
                if ($link->getRel() instanceof DeleteInterface) {
                    $this->set($link->getRel()->delete($model, $link));
                }
            });
    }

    public function addInsertedLinks()
    {
        return $this
            ->eachLink(function (AbstractModel $model, AbstractLink $link) {
                if ($link->getRel() instanceof InsertInterface) {
                    $this->set($link->getRel()->insert($model, $link));
                }
            });
    }

    public function updateLinks()
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
        $this->addDeletedLinks();

        self::persist($this->getDeleted(), [NodeEvent::DELETE], function (AbstractRepo $repo, SplObjectStorage $models) {
            $repo->delete($models);
        });

        $this->addInsertedLinks();


        self::persist($this->getPending(), [NodeEvent::INSERT, NodeEvent::SAVE], function (AbstractRepo $repo, SplObjectStorage $models) {
            $repo->insert($models);
        });

        $this->updateLinks();

        self::persist($this->getChanged(), [NodeEvent::UPDATE, NodeEvent::SAVE], function (AbstractRepo $repo, SplObjectStorage $models) {
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
