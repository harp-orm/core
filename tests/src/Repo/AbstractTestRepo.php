<?php

namespace CL\LunaCore\Test\Repo;

use CL\LunaCore\Repo\AbstractRepo;
use CL\LunaCore\Model\AbstractModel;
use SplObjectStorage;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractTestRepo extends AbstractRepo
{
    private $file;

    public function selectWithId($key)
    {
        $contents = $this->getContents();

        if (isset($contents[$key])) {
            return $this->newInstance($contents[$key], AbstractModel::PERSISTED);
        }
    }

    public function findByKey($searchKey, array $values)
    {
        $contents = $this->getContents();
        $found = array();

        foreach ($contents as $properties) {
            if (in_array($properties[$searchKey], $values)) {
                $found []= $this->newInstance($properties, AbstractModel::PERSISTED);
            }
        }

        return $this->getIdentityMap()->getArray($found);
    }

    public function getContents()
    {
        return json_decode(file_get_contents($this->file), true);
    }

    public function setContents(array $contents)
    {
        file_put_contents($this->file, json_encode($contents, JSON_PRETTY_PRINT));

        return $this;
    }

    public function update(SplObjectStorage $models)
    {
        $contents = $this->getContents();

        foreach ($models as $model) {
            $contents[$model->getId()] = $model;
        }

        $this->setContents($contents);
    }

    public function delete(SplObjectStorage $models)
    {
        $contents = $this->getContents();

        foreach ($models as $model) {
            unset($contents[$model->getId()]);
        }

        $this->setContents($contents);
    }

    public function insert(SplObjectStorage $models)
    {
        $contents = $this->getContents();

        foreach ($models as $model) {
            $id = $contents ? max(array_keys($contents)) + 1 : 1;

            $model
                ->setId($id)
                ->resetOriginals()
                ->setStateNotVoid();

            $contents[$id] = $model;
        }

        $this->setContents($contents);
    }

    public function __construct($modelClass)
    {
        parent::__construct($modelClass);

        $this->file = __DIR__.'/../../repos/'.$this->getName().'.json';
    }
}
