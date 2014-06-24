<?php

namespace Harp\Core\Model;

use Harp\Core\Repo\AbstractRepo;
use Harp\Core\Repo\LinkOne;
use LogicException;

/**
 * Gives the model methods for accessing the corresponding repo
 * As well as a static interface for loading / saving models
 *
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
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
     * @return AbstractModel
     */
    public static function find($id)
    {
        return static::getRepoStatic()->find($id);
    }

    /**
     * @param  mixed $id
     * @return AbstractModel
     */
    public static function findByName($name)
    {
        return static::getRepoStatic()->findByName($name);
    }

    /**
     * @return \Harp\Core\Save\AbstractFind
     */
    public static function findAll()
    {
        return static::getRepoStatic()->findAll();
    }

    /**
     * @param  mixed $id
     * @return AbstractRepo
     */
    public static function save(AbstractModel $model)
    {
        return static::getRepoStatic()->save($model);
    }

    /**
     * @param  mixed $id
     * @return AbstractRepo
     */
    public static function onlyDeleted()
    {
        return static::getRepoStatic()->findAll()->onlyDeleted();
    }

    /**
     * @param  mixed $id
     * @return AbstractRepo
     */
    public static function onlySaved()
    {
        return static::getRepoStatic()->findAll()->onlySaved();
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
     * Shortcut method to Repo's loadLink. Throws LogicException if the link is void
     *
     * @param  string         $name
     * @return \Harp\Core\Repo\AbstractLink
     * @throws LogicException If link is void
     */
    public function getLinkOrError($name)
    {
        $link = $this->getLink($name);

        if ($link instanceof LinkOne and $link->get()->isVoid()) {
            throw new LogicException(
                sprintf('Link for rel %s should not be void', $name)
            );
        }

        return $link;
    }
}