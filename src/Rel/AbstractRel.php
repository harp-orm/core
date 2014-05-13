<?php

namespace CL\LunaCore\Rel;

use CL\LunaCore\Model\AbstractModel;
use CL\LunaCore\Repo\AbstractRepo;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractRel
{
    protected $name;
    protected $foreignRepo;
    protected $repo;

    abstract public function areLinked(AbstractModel $model, AbstractModel $foreignModel);
    abstract public function hasForeign(array $models);
    abstract public function loadForeign(array $models);
    abstract public function linkToForeign(array $models, array $foreign);

    public function __construct($name, AbstractRepo $repo, AbstractRepo $foreignRepo, array $options = array())
    {
        $this->name = $name;
        $this->foreignRepo = $foreignRepo;
        $this->repo = $repo;

        foreach ($options as $name => $value) {
            $this->$name = $value;
        }
    }

    public function getName()
    {
        return $this->name;
    }

    public function getRepo()
    {
        return $this->repo;
    }

    public function getForeignRepo()
    {
        return $this->foreignRepo;
    }

    public function loadForeignForNodes(array $models)
    {
        if ($this->hasForeign($models)) {
            return $this->loadForeign($models);
        } else {
            return array();
        }
    }
}
