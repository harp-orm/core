<?php

namespace CL\LunaCore\Test\Repo;

use CL\LunaCore\Model\State;
use CL\LunaCore\Save\AbstractFind;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Find extends AbstractFind
{
    public $conditions = array();
    public $limit = null;
    public $offset = 0;

    public function where($property, $value)
    {
        $this->conditions[$property] = $value;

        return $this;
    }

    public function whereNot($property, $value)
    {
        $this->conditions[$property] = new Not($value);

        return $this;
    }

    public function whereIn($property, array $value)
    {
        $this->conditions[$property] = $value;

        return $this;
    }

    public function clearWhere()
    {
        $this->conditions = array();

        return $this;
    }

    public function limit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    public function clearLimit()
    {
        $this->limit = null;

        return $this;
    }

    public function offset($offset)
    {
        $this->offset = $offset;

        return $this;
    }

    public function clearOffset()
    {
        $this->offset = 0;

        return $this;
    }

    public function execute()
    {
        $contents = $this->getRepo()->getContents();

        $found = array();

        foreach ($contents as $params) {

            $foundModel = true;
            foreach ($this->conditions as $property => $value) {
                if (! array_key_exists($property, $params)) {
                    $foundModel = false;
                    break;
                }

                if (is_array($value)) {
                    if (! in_array($params[$property], $value)) {
                        $foundModel = false;
                        break;
                    }
                } elseif ($value instanceof Not) {
                    if ($params[$property] === $value->getValue()) {
                        $foundModel = false;
                        break;
                    }
                } else {
                    if ($params[$property] !== $value) {
                        $foundModel = false;
                        break;
                    }
                }
            }

            if ($foundModel) {
                if ($this->getRepo()->getInherited()) {
                    $class = $params['class'];
                    $model = new $class($params, State::SAVED);
                } else {
                     $model = $this->getRepo()->newInstance($params, State::SAVED);
                }

                $found []= $model;
            }
        }

        return array_slice($found, $this->offset, $this->limit);
    }

}
