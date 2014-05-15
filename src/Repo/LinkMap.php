<?php

namespace CL\LunaCore\Repo;

use SplObjectStorage;
use CL\LunaCore\Model\AbstractModel;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class LinkMap
{
    /**
     * @var SplObjectStorage
     */
    private $map;

    function __construct()
    {
        $this->map = new SplObjectStorage();
    }

    /**
     * Get Links object associated with this model.
     * If there is none, an empty Links object is created.
     *
     * @param  AbstractModel $model
     * @return Links
     */
    public function get(AbstractModel $model)
    {
        if ($this->map->contains($model)) {
            return $this->map[$model];
        } else {
            return $this->map[$model] = new Links($model);
        }
    }

    /**
     * @param  AbstractModel $model
     * @return boolean
     */
    public function isEmpty(AbstractModel $model)
    {
        return (! $this->map->contains($model) or $this->map[$model]->isEmpty());
    }

    /**
     * @param  AbstractModel $model
     * @return boolean
     */
    public function has(AbstractModel $model)
    {
        return $this->map->contains($model);
    }
}
