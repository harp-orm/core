<?php

namespace CL\LunaCore\Repo;

use InvalidArgumentException;
use ReflectionClass;
use CL\LunaCore\Model\AbstractModel;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class IdentityMap
{
    private $models;
    private $modelClass;

    public function __construct(ReflectionClass $modelClass)
    {
        $this->modelClass = $modelClass;
    }

    public function get(AbstractModel $model)
    {
        if ( ! $this->modelClass->isInstance($model)) {
            throw new InvalidArgumentException(
                sprintf('Node Must be of %s', $this->modelClass->getName())
            );
        }

        if ($model->isPersisted()) {
            $key = $model->getId();

            if (isset($this->models[$key])) {
                $model = $this->models[$key];
            } else {
                $this->models[$key] = $model;
            }

        }

        return $model;
    }

    public function getArray(array $models)
    {
        return array_map(function($model){
            return $this->get($model);
        }, $models);
    }

    public function clear()
    {
        $this->models = null;
    }
}
