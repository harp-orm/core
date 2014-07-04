<?php

namespace Harp\Core\Save;

use Harp\Core\Model\AbstractModel;
use Harp\Core\Model\State;
use Harp\Core\Repo\RepoModels;
use InvalidArgumentException;

/**
 * This class provides a common interface for retrieving models.
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
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
     * @var int
     */
    private $flags = State::SAVED;

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
     * Add a constrint to return both soft deleted and saved models
     *
     * @return AbstractFind $this
     */
    public function deletedAndSaved()
    {
        $this->setFlags(State::DELETED | State::SAVED);

        return $this;
    }

    /**
     * Add a constrint to only return soft deleted models
     *
     * @return AbstractFind $this
     */
    public function onlyDeleted()
    {
        $this->setFlags(State::DELETED);

        return $this;
    }

    /**
     * Add a constrint to only return models that are not soft deleted
     *
     * @return AbstractFind $this
     */
    public function onlySaved()
    {
        $this->setFlags(State::SAVED);

        return $this;
    }

    /**
     * You can pass State::DELETED to retrieve only deleted
     * and State::DELETED | State::SAVED to retrieve deleted + saved
     *
     * @param  int          $flags
     * @return AbstractFind $this
     */
    public function setFlags($flags)
    {
        if ($flags !== null) {
            if (! in_array($flags, [State::SAVED, State::DELETED, State::DELETED | State::SAVED], true)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Flags were %s, but need to be State::SAVED, State::DELETED or State::DELETED | State::SAVED',
                        $flags
                    )
                );
            }

            $this->flags = $flags;
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getFlags()
    {
        return $this->flags;
    }

    /**
     * Apply the previously set flags
     *
     * @return AbstractFind $this
     */
    public function applyFlags()
    {
        if ($this->getRepo()->getSoftDelete()) {
            if ($this->flags === State::SAVED) {
                $this->where('deletedAt', null);
            } elseif ($this->flags === State::DELETED) {
                $this->whereNot('deletedAt', null);
            }
        }

        return $this;
    }

    /**
     * @return AbstractModel[]
     */
    public function loadRaw()
    {
        $models = $this->applyFlags()->execute();

        return $models;
    }

    /**
     * Calls "loadRaw" and passes the result through an IdentityMap
     *
     * @return RepoModels
     */
    public function load()
    {
        $models = $this->loadRaw();

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
     * @param  array      $rels
     * @return RepoModels
     */
    public function loadWith(array $rels)
    {
        $models = $this->load();

        $this->getRepo()->loadAllRelsFor($models, $rels, $this->flags);

        return $models;
    }

    /**
     * @return array
     */
    public function loadIds()
    {
        return $this->load()->getIds();
    }

    /**
     * @return int
     */
    public function loadCount()
    {
        return count($this->loadRaw());
    }

    /**
     * Will return a void model if no model is found.
     *
     * @return AbstractModel
     */
    public function loadFirst()
    {
        return $this->limit(1)->load()->getFirst();
    }
}
