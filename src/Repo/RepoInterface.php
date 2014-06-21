<?php

namespace Harp\Core\Repo;

/**
 * This interface allows adding a requirement for static methods on classes that implements it.
 * This is a workaround for not having abstract static methods.
 *
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
interface RepoInterface
{
    /**
     * @return AbstractRepo
     */
    public static function newInstance();
}
