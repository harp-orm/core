<?php

namespace Harp\Core\Rel;

use Harp\Core\Model\AbstractModel;
use Harp\Core\Model\Models;
use Harp\Core\Repo\AbstractRepo;
use Closure;

/**
 * The base class for all the relations. Actual relations should extend AbstractRelMany or AbstractRelOne.
 * The main idea is to load all the models associated with a given set of models.
 * That way eager loading works out of the box.
 *
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
     * Foreign repo is used to allow you to correctly return "void" models.
     * Even if your relation is polymorphic and can link to different repos, you should
     * provide a default repo.
     *
     * @param string       $name        Unique repo name
     * @param AbstractRepo $repo
     * @param AbstractRepo $foreignRepo
     * @param array        $properties   Added as is to the rel's properties.
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

    /**
     * Iterate models and foreign models one by one and and assign links based on the areLinked method
     * Yeild the resulted links one by one for further processing.
     *
     * @param  Models  $models
     * @param  Models  $foreign
     * @param  Closure $yield   call for each link
     */
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
