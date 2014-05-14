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
    /**
     * @var string
     */
    protected $name;

    /**
     * @var AbstractRepo
     */
    protected $foreignRepo;

    /**
     * @var AbstractRepo
     */
    protected $repo;

    abstract public function areLinked(AbstractModel $model, AbstractModel $foreignModel);
    abstract public function hasForeign(array $models);
    abstract public function loadForeign(array $models);
    abstract public function linkToForeign(array $models, array $foreign);

    /**
     * @param string       $name
     * @param AbstractRepo $repo
     * @param AbstractRepo $foreignRepo
     * @param array        $properties
     */
    public function __construct($name, AbstractRepo $repo, AbstractRepo $foreignRepo, array $properties = array())
    {
        $this->name = $name;
        $this->foreignRepo = $foreignRepo;
        $this->repo = $repo;

        foreach ($properties as $name => $value) {
            $this->$name = $value;
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return AbstractRepo
     */
    public function getRepo()
    {
        return $this->repo;
    }

    /**
     * @return AbstractRepo
     */
    public function getForeignRepo()
    {
        return $this->foreignRepo;
    }

    /**
     * @param AbstractModel[] $models
     * @return AbstractModel[]
     */
    public function loadForeignForNodes(array $models)
    {
        if ($this->hasForeign($models)) {
            return $this->loadForeign($models);
        } else {
            return [];
        }
    }
}
