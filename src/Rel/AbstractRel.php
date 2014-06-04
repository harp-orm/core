<?php

namespace Harp\Core\Rel;

use Harp\Core\Model\AbstractModel;
use Harp\Core\Model\Models;
use Harp\Core\Repo\AbstractRepo;
use Closure;

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
    private $name;

    /**
     * @var AbstractRepo
     */
    private $foreignRepo;

    /**
     * @var AbstractRepo
     */
    private $repo;

    abstract public function areLinked(AbstractModel $model, AbstractModel $foreignModel);
    abstract public function hasForeign(Models $models);
    abstract public function loadForeign(Models $models, $flags = null);
    abstract public function newLinkFrom(AbstractModel $model, array $links);

    /**
     * @param string       $name
     * @param AbstractRepo $repo
     * @param AbstractRepo $foreignRepo
     * @param array        $properties
     */
    public function __construct(
        $name,
        AbstractRepo $repo,
        AbstractRepo $foreignRepo,
        array $properties = array()
    ) {
        $this->name = $name;
        $this->repo = $repo;
        $this->foreignRepo = $foreignRepo;

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
     * @param  Models $models
     * @return Models
     */
    public function loadForeignModels(Models $models, $flags = null)
    {
        if ($this->hasForeign($models)) {
            $foreign = $this->loadForeign($models, $flags);

            return new Models($foreign);
        } else {
            return new Models();
        }
    }

    public function linkModels(Models $models, Models $foreign, Closure $yield)
    {
        foreach ($models as $model) {

            $linked = [];

            foreach ($foreign as $foreignModel) {
                if ($this->areLinked($model, $foreignModel)) {
                    $linked []= $foreignModel;
                }
            }

            $link = $this->newLinkFrom($model, $linked);

            $yield($link);
        }
    }
}
