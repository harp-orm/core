<?php

namespace Harp\Core\Save;

use Harp\Core\Model\AbstractModel;
use Harp\Core\Model\State;
use Harp\Core\Repo\Event;
use Harp\Core\Repo\RepoModels;
use InvalidArgumentException;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractFind
{
    /**
     * @param  string       $property
     * @param  mixed        $value
     * @return AbstractFind $this
     */
    abstract public function where($property, $value);

    /**
     * @param  string       $property
     * @param  mixed        $value
     * @return AbstractFind $this
     */
    abstract public function whereNot($property, $value);

    /**
     * @param  string       $property
     * @param  array        $value
     * @return AbstractFind $this
     */
    abstract public function whereIn($property, array $value);

    /**
     * @return AbstractFind $this
     */
    abstract public function clearWhere();

    /**
     * @param  int          $limit
     * @return AbstractFind $this
     */
    abstract public function limit($limit);

    /**
     * @return AbstractFind $this
     */
    abstract public function clearLimit();

    /**
     * @param  int          $offset
     * @return AbstractFind $this
     */
    abstract public function offset($offset);

    /**
     * @return AbstractFind $this
     */
    abstract public function clearOffset();

    /**
     * @return AbstractModel[]
     */
    abstract public function execute();

    /**
     * @var AbstractSaveRepo
     */
    private $repo;

    public function __construct(AbstractSaveRepo $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @return AbstractSaveRepo
     */
    public function getRepo()
    {
        return $this->repo;
    }

    /**
     * @param  mixed        $value
     * @return AbstractFind $this
     */
    public function whereKey($value)
    {
        $property = $this->getRepo()->getPrimaryKey();

        $this->where($property, $value);

        return $this;
    }

    /**
     * @param  mixed        $values
     * @return AbstractFind $this
     */
    public function whereKeys(array $values)
    {
        $property = $this->getRepo()->getPrimaryKey();

        $this->whereIn($property, $values);

        return $this;
    }

    public function onlySaved()
    {
        $this->where('deletedAt', null);

        return $this;
    }

    public function onlyDeleted()
    {
        $this->whereNot('deletedAt', null);

        return $this;
    }

    public function applyFlags($flags)
    {
        if ($this->getRepo()->getSoftDelete()) {
            if ($flags === null) {
                $this->onlySaved();
            } elseif ($flags & State::DELETED) {
                $this->onlyDeleted();
            } elseif (! ($flags & (State::DELETED | State::SAVED))) {
                throw new InvalidArgumentException('Use "State::DELETED" or "State::DELETED | State::SAVED"');
            }
        }

        return $this;
    }

    /**
     * @return AbstractModel[]
     */
    public function loadRaw($flags = null)
    {
        $models = $this->applyFlags($flags)->execute();

        return $models;
    }

    /**
     * @return RepoModels
     */
    public function load($flags = null)
    {
        $models = $this->loadRaw($flags);

        foreach ($models as & $model) {
            $model = $model->getRepo()->getIdentityMap()->get($model);
        }

        return new RepoModels($this->repo, $models);
    }

    /**
     * @param  array  $rels
     * @return RepoModels
     */
    public function loadWith(array $rels, $flags = null)
    {
        $models = $this->load($flags);

        $this->getRepo()->loadAllRelsFor($models, $rels, $flags);

        return $models;
    }

    /**
     * @return array
     */
    public function loadIds($flags = null)
    {
        return $this->load($flags)->getIds();
    }

    /**
     * @return int
     */
    public function loadCount($flags = null)
    {
        return count($this->loadRaw($flags));
    }

    /**
     * @return AbstractModel
     */
    public function loadFirst($flags = null)
    {
        return $this->limit(1)->load($flags)->getFirst();
    }
}
