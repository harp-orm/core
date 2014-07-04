<?php

namespace Harp\Core\Repo;

/**
 * A dependancy injection container for Repo objects
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Container
{
    /**
     * Holds all the singleton repo instances.
     * Use the name of the class as array key.
     *
     * @var array
     */
    private static $repos;

    /**
     * @param  string       $class
     * @return AbstractRepo
     */
    public static function get($class)
    {
        if (! self::has($class)) {
            self::set($class, $class::newRepo($class));
        }

        return self::$repos[$class];
    }

    /**
     * @param string       $class
     * @param AbstractRepo $repo
     */
    public static function set($class, AbstractRepo $repo)
    {
        self::$repos[$class] = $repo;
    }

    /**
     * @param  string  $class
     * @return boolean
     */
    public static function has($class)
    {
        return isset(self::$repos[$class]);
    }

    public static function clear()
    {
        self::$repos = [];
    }
}
