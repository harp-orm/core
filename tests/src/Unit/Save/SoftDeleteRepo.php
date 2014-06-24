<?php

namespace Harp\Core\Test\Unit\Save;

use Harp\Core\Save\AbstractSaveRepo;
use Harp\Core\Model\Models;
use BadMethodCallException;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class SoftDeleteRepo extends AbstractSaveRepo
{
    private static $instance;

    /**
     * @return User
     */
    public static function get()
    {
        if (! self::$instance) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public static function set(SoftDeleteRepo $repo)
    {
        self::$instance = $repo;
    }

    public static function clearInstance()
    {
        self::$instance = null;
    }

    public function initialize()
    {
        $this
            ->setModelClass(__NAMESPACE__.'\SoftDeleteModel')
            ->setSoftDelete(true);
    }

    public function findAll()
    {
        throw new BadMethodCallException('Test Repo: cannot call findAll');
    }

    public function update(Models $models)
    {
        throw new BadMethodCallException('Test Repo: cannot call update');
    }

    public function delete(Models $models)
    {
        throw new BadMethodCallException('Test Repo: cannot call delete');
    }

    public function insert(Models $models)
    {
        throw new BadMethodCallException('Test Repo: cannot call insert');
    }
}
