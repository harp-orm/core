<?php

namespace CL\LunaCore\Repo;

use SplObjectStorage;
use Closure;

use CL\LunaCore\Model\AbstractModel;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Links
{
    protected $model;
    protected $items = array();

    function __construct(AbstractModel $model)
    {
        $this->model = $model;
    }

    public function getNode()
    {
        return $this->model;
    }

    public function add($name, AbstractLink $link)
    {
        $this->items[$name] = $link;

        return $this;
    }

    public function all()
    {
        return $this->items;
    }

    public function getNodes()
    {
        $all = new SplObjectStorage();

        foreach ($this->items as $item) {
            $all->addAll($item->getAll());
        }

        return $all;
    }

    public function has($name)
    {
        return isset($this->items[$name]);
    }

    public function get($name)
    {
        if ($this->has($name)) {
            return $this->items[$name];
        }
    }
}
