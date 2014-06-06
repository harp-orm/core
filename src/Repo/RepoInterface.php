<?php

namespace Harp\Core\Repo;

/**
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
