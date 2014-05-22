<?php

namespace CL\LunaCore\Save;

use CL\LunaCore\Model\AbstractModel;
use CL\LunaCore\Model\Models;
use CL\LunaCore\Model\State;
use CL\LunaCore\Repo\Event;
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

    /**
     * @return AbstractModel[]
     */
    public function loadRaw($state = null)
    {
        if ($this->getRepo()->getSoftDelete()) {
            if ($state === null) {
                $this->where('deletedAt', null);
            } elseif ($state & State::DELETED) {
                $this->whereNot('deletedAt', null);
            } elseif (! ($state & (State::DELETED | State::SAVED))) {
                throw new InvalidArgumentException('Use "State::DELETED" or "State::DELETED | State::SAVED"');
            }
        }

        $models = $this->execute();

        foreach ($models as $model) {
            $this->getRepo()->dispatchAfterEvent($model, Event::LOAD);
        }

        return $models;
    }

    /**
     * @return Models
     */
    public function load($state = null)
    {
        $models = $this->loadRaw($state);
        $models = $this->getRepo()->getIdentityMap()->getArray($models);

        return new Models($models);
    }

    /**
     * @param  array  $rels
     * @return Models
     */
    public function loadWith(array $rels, $state = null)
    {
        $models = $this->load($state);

        $this->getRepo()->loadAllRelsFor($models, $rels, $state);

        return $models;
    }

    /**
     * @return array
     */
    public function loadIds($state = null)
    {
        return $this->load($state)->pluckProperty($this->getRepo()->getPrimaryKey());
    }

    /**
     * @return int
     */
    public function loadCount($state = null)
    {
        return count($this->loadRaw($state));
    }

    /**
     * @return AbstractModel
     */
    public function loadFirst($state = null)
    {
        $items = $this->limit(1)->load($state);
        $items->rewind();

        return $items->current() ?: $this->getRepo()->newVoidInstance();
    }
}
