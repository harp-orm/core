<?php

namespace Harp\Core\Test\Repo;

use Harp\Core\Save\AbstractSaveRepo;
use Harp\Core\Model\Models;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractTestRepo extends AbstractSaveRepo
{
    private $file;

    public function getFile()
    {
        return $this->file;
    }

    public function setFile($file)
    {
        $this->file = __DIR__.'/../../repos/'.$file;

        return $this;
    }

    public function findAll()
    {
        return new Find($this);
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

    public function update(Models $models)
    {
        $contents = $this->getContents();

        foreach ($models as $model) {
            $data = $model->getProperties();
            $contents[$model->getId()] = $this->getSerializers()->serialize($data);
        }

        $this->setContents($contents);

        return $this;
    }

    public function delete(Models $models)
    {
        $contents = $this->getContents();

        foreach ($models as $model) {
            unset($contents[$model->getId()]);
        }

        $this->setContents($contents);

        return $this;
    }

    public function insert(Models $models)
    {
        $contents = $this->getContents();

        foreach ($models as $model) {
            $id = $contents ? max(array_keys($contents)) + 1 : 1;

            $data = $model->setId($id)->getProperties();

            $contents[$id] = $this->getSerializers()->serialize($data);
        }

        $this->setContents($contents);

        return $this;
    }
}
