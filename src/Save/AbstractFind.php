<?php

namespace CL\LunaCore\Save;

use CL\LunaCore\Model\AbstractModel;
use CL\LunaCore\Model\Models;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractFind
{
    /**
     * @param  string $property
     * @param  mixed $value
     * @return AbstractFind $this
     */
    abstract public function where($property, $value);

    /**
     * @param  string $property
     * @param  mixed $value
     * @return AbstractFind $this
     */
    abstract public function whereNot($property, $value);

    /**
     * @param  string $property
     * @param  array $value
     * @return AbstractFind $this
     */
    abstract public function whereIn($property, array $value);

    /**
     * @param  int $limit
     * @return AbstractFind $this
     */
    abstract public function limit($limit);

    /**
     * @param  int $offset
     * @return AbstractFind $this
     */
    abstract public function offset($offset);

    /**
     * @return \CL\LunaCore\Model\AbstractModel
     */
    abstract public function execute();

    /**
     * @param  AbstractSaveRepo $repo
     * @param  Models  $models
     * @param  array            $rels
     * @param  int              $state
     */
    protected static function loadRels(AbstractSaveRepo $repo, Models $models, array $rels, $state = null)
    {
        foreach ($rels as $relName => $childRels) {
            $rel = $repo->getRel($relName);
            $foreign = $repo->loadRel($relName, $models, $state);

            if ($childRels) {
                self::loadRels($rel->getForeignRepo(), $foreign, $childRels, $state);
            }
        }
    }

    /**
     * @var AbstractSaveRepo
     */
    private $repo;

    /**
     * @var array
     */
    private $conditions = array();

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
     * @param  mixed  $value
     * @return AbstractFind $this
     */
    public function whereKey($value)
    {
        $property = $this->getRepo()->getPrimaryKey();

        $this->where($property, $value);

        return $this;
    }

    /**
     * @param  mixed  $value
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
            } elseif ($state & (State::DELETED | State::SAVED)) {

            } else {
                throw new InvalidArgument('Use "State::DELETED" or "State::DELETED | State::SAVED"');
            }
        }

        return $this->execute();
    }

    /**
     * @return Models
     */
    public function load($state = null)
    {
        $found = new Models();

        foreach ($this->loadRaw($state) as $model) {
            $found->add(
                $this->getRepo()->getIdentityMap()->getArray($model)
            );
        }

        return $found;
    }

    /**
     * @return Models
     */
    public function loadWith($rels, $state = null)
    {
        $models = $this->load($state);

        $rels = Arr::toAssoc((array) $rels);

        self::loadRels($this->getRepo(), $models, $rels, $state);

        return $models;
    }

    /**
     * @return array
     */
    public function loadIds()
    {
        return Arr::pluckProperty($this->getRepo()->getPrimaryKey(), $this->loadRaw());
    }

    /**
     * @return int
     */
    public function loadCount()
    {
        return count($this->loadRaw());
    }

    /**
     * @return AbstractModel
     */
    public function loadFirst()
    {
        $items = $this->limit(1)->load();

        return reset($items) ?: $this->getRepo()->newVoidInstance();
    }
}
