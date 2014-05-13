<?php

namespace CL\LunaCore\Repo;

use CL\LunaCore\Rel\AbstractRel;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Rels {

    public function add(AbstractRel $item)
    {
        $this->items[$item->getName()] = $item;

        return $this;
    }

    protected $items;

    public function all()
    {
        return $this->items;
    }

    public function set(array $items)
    {
        foreach ($items as $item) {
            $this->add($item);
        }

        return $this;
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

    public function isEmpty()
    {
        return empty($this->items);
    }
}
