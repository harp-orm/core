<?php

namespace Harp\Core\Model;

/**
 * Gives the model methods for accessing the corresponding repo
 * As well as a static interface for loading / saving models
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
interface RepoInterface
{
    public static function findAll();
    public static function newRepo($class);
}
