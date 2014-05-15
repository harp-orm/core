<?php

namespace CL\LunaCore\Repo;

use CL\LunaCore\Rel\AbstractRel;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Rels
{
    protected $items = array();

    /**
     * @param  AbstractRel $item
     * @return Rels        $this
     */
    public function add(AbstractRel $item)
    {
        $this->items[$item->getName()] = $item;

        return $this;
    }

    /**
     * @return AbstractRel[]
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * @param  AbstractRel[] $items
     * @return Rels          $this
     */
    public function set(array $items)
    {
        foreach ($items as $item) {
            $this->add($item);
        }

        return $this;
    }

    /**
     * @param  string  $name
     * @return boolean
     */
    public function has($name)
    {
        return isset($this->items[$name]);
    }

    /**
     * @param  string           $name
     * @return AbstractRel|null
     */
    public function get($name)
    {
        if ($this->has($name)) {
            return $this->items[$name];
        }
    }

    /**
     * @return boolean
     */
    public function isEmpty()
    {
        return empty($this->items);
    }
}
