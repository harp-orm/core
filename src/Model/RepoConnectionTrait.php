<?php

namespace Harp\Core\Model;

use Harp\Core\Repo\AbstractRepo;
use Harp\Core\Repo\LinkOne;
use Harp\Core\Repo\LinkMany;
use LogicException;

/**
 * Gives the model methods for accessing the corresponding repo
 * As well as a static interface for loading / saving models
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
trait RepoConnectionTrait
{
    /**
     * @return AbstractRepo
     */
    public static function getRepoStatic()
    {
        return AbstractRepo::getInstance(static::REPO);
    }

    /**
     * @return AbstractRepo
     */
    public function getRepo()
    {
        return AbstractRepo::getInstance(static::REPO);
    }

    /**
     * @param  mixed $id
     * @param  int   $flags
     * @return AbstractModel
     */
    public static function find($id, $flags = null)
    {
        return static::getRepoStatic()->find($id, $flags);
    }

    /**
     * @param  string $name
     * @param  int    $flags
     * @return AbstractModel
     */
    public static function findByName($name, $flags = null)
    {
        return static::getRepoStatic()->findByName($name, $flags);
    }

    /**
     * @return \Harp\Core\Save\AbstractFind
     */
    public static function findAll()
    {
        return static::getRepoStatic()->findAll();
    }

    /**
     * @param  AbstractModel $model
     * @return AbstractRepo
     */
    public static function save(AbstractModel $model)
    {
        return static::getRepoStatic()->save($model);
    }

    /**
     * @param  AbstractModel[] $models
     * @return AbstractRepo
     */
    public static function saveArray(array $models)
    {
        return static::getRepoStatic()->saveArray($models);
    }

    /**
     * Property defined by Repo Primary Key
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->{$this->getRepo()->getPrimaryKey()};
    }

    /**
     * Set property defined by Repo Primary Key
     *
     * @param  mixed
     */
    public function setId($id)
    {
        $this->{$this->getRepo()->getPrimaryKey()} = $id;

        return $this;
    }

    /**
     * Shortcut method to Repo's loadLink
     *
     * @param  string       $name
     * @return \Harp\Core\Repo\AbstractLink
     */
    public function getLink($name)
    {
        return $this->getRepo()->loadLink($this, $name);
    }

    /**
     * @param  string $name
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
     * @param  string $name
     * @return AbstractModel
     */
    public function getLinkedModel($name)
    {
        return $this->getLinkOne($name)->get();
    }

    /**
     * @param string $name
     * @param AbstractModel $model
     */
    public function setLinkedModel($name, $model)
    {
        $this->getLinkOne($name)->set($model);

        return $this;
    }

    /**
     * @param  string $name
     * @return LinkMany
     */
    public function getLinkMany($name)
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
