<?php

namespace CL\LunaCore\Repo;

use SplObjectStorage;
use Closure;

use CL\LunaCore\Util\Objects;
use CL\LunaCore\Model\AbstractModel;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class LinkMap
{
    private $map;

    function __construct()
    {
        $this->map = new SplObjectStorage();
    }

    public function get(AbstractModel $model)
    {
        if ($this->map->contains($model)) {
            return $this->map[$model];
        } else {
            return $this->map[$model] = new Links($model);
        }
    }

    public function isEmpty(AbstractModel $model)
    {
        return (! $this->map->contains($model) or $this->map[$model]->isEmpty());
    }

    public function has(AbstractModel $model)
    {
        return $this->map->contains($model);
    }
}
