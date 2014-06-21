<?php

namespace Harp\Core\Save;

use Harp\Core\Model\AbstractModel;
use Harp\Core\Model\State;
use Harp\Core\Repo\RepoModels;
use InvalidArgumentException;

/**
 * This class provides a common interface for retrieving models.
 *
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

    /**
     * @param AbstractSaveRepo $repo
     */
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
     * Use the Primary key for the "name" part of the where constraint
     *
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
     * Use the Primary key for the "name" part of the where constraint
     *
     * @param  mixed        $values
     * @return AbstractFind $this
     */
    public function whereKeys(array $values)
    {
        $property = $this->getRepo()->getPrimaryKey();

        $this->whereIn($property, $values);

        return $this;
    }

    /**
     * Add a constrint not to return soft deleted models
     *
     * @return AbstractFind $this
     */
    public function onlySaved()
    {
        $this->where('deletedAt', null);

        return $this;
    }

    /**
     * Add a constrint to only return soft deleted models
     *
     * @return AbstractFind $this
     */
    public function onlyDeleted()
    {
        $this->whereNot('deletedAt', null);

        return $this;
    }

    /**
     * You can pass State::DELETED to retrieve only deleted
     * and State::DELETED | State::SAVED to retrieve deleted + saved
     *
     * @param  int          $flags
     * @return AbstractFind $this
     */
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
     * Calls "loadRaw" and passes the result through an IdentityMap
     *
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
     * Eager load relations.
     *
     * Example:
     *   ->loadWith(['user' => 'profile'])
     *
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
     * Will return a void model if no model is found.
     *
     * @return AbstractModel
     */
    public function loadFirst($flags = null)
    {
        return $this->limit(1)->load($flags)->getFirst();
    }
}
