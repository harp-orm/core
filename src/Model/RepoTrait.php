<?php

namespace Harp\Core\Model;

use Harp\Core\Repo\AbstractRepo;
use Harp\Core\Repo\LinkOne;
use Harp\Core\Repo\LinkMany;
use Harp\Core\Repo\Container;
use Harp\Core\Save\Save;
use LogicException;

/**
 * Gives the model methods for accessing the corresponding repo
 * As well as a static interface for loading / saving models
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
trait RepoTrait
{
    /**
     * @return AbstractRepo
     */
    public static function getRepo()
    {
        return Container::get(get_called_class());
    }

    /**
     * Get the primaryKey from the repo
     *
     * @return string
     */
    public static function getPrimaryKey()
    {
        return self::getRepo()->getPrimaryKey();
    }

    /**
     * Get the nameKey from the repo
     *
     * @return string
     */
    public static function getNameKey()
    {
        return self::getRepo()->getNameKey();
    }

    /**
     * @param  string|int    $id
     * @param  int           $flags
     * @return AbstractModel
     */
    public static function find($id, $flags = null)
    {
        return static::findAll()
            ->where(self::getPrimaryKey(), $id)
            ->setFlags($flags)
            ->loadFirst();
    }

    /**
     * @param  string        $name
     * @param  int           $flags
     * @return AbstractModel
     */
    public static function findByName($name, $flags = null)
    {
        return static::findAll()
            ->where(self::getNameKey(), $name)
            ->setFlags($flags)
            ->loadFirst();
    }

    /**
     * Persist the model in the database
     *
     * @param AbstractModel $model
     */
    public static function save(AbstractModel $model)
    {
        (new Save())
            ->add($model)
            ->execute();
    }

    /**
     * Persist an array of models in the database
     *
     * @param AbstractModel[] $models
     */
    public static function saveArray(array $models)
    {
        (new Save())
            ->addArray($models)
            ->execute();
    }

    /**
     * Property defined by Repo Primary Key
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->{self::getPrimaryKey()};
    }

    /**
     * Set property defined by Repo Primary Key
     *
     * @param  mixed
     */
    public function setId($id)
    {
        $this->{self::getPrimaryKey()} = $id;

        return $this;
    }

    /**
     * Shortcut method to Repo's loadLink
     *
     * @param  string                       $name
     * @return \Harp\Core\Repo\AbstractLink
     */
    public function getLink($name)
    {
        return self::getRepo()->loadLink($this, $name);
    }

    /**
     * @param  string  $name
     * @return LinkOne
     */
    public function getLinkOne($name)
    {
        $link = $this->getLink($name);

        if (! $link instanceof LinkOne) {
            throw new LogicException(
                sprintf('Rel %s for %s must be a valid RelOne', $name, get_class($this))
            );
        }

        return $link;
    }

    /**
     * @param  string        $name
     * @return AbstractModel
     */
    public function get($name)
    {
        return $this->getLinkOne($name)->get();
    }

    /**
     * @param string        $name
     * @param AbstractModel $model
     */
    public function set($name, $model)
    {
        $this->getLinkOne($name)->set($model);

        return $this;
    }

    /**
     * @param  string   $name
     * @return LinkMany
     */
    public function all($name)
    {
        $link = $this->getLink($name);

        if (! $link instanceof LinkMany) {
            throw new LogicException(
                sprintf('Rel %s for %s must be a valid RelMany', $name, get_class($this))
            );
        }

        return $link;
    }
}
