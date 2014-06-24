<?php

namespace Harp\Core\Test\Unit\Model;

use Harp\Core\Model\AbstractModel;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class ModelMock extends AbstractModel
{
    const REPO = 'Harp\Core\Test\Unit\Model\RepoMock';

    private static $repo;

    public static function setRepoStatic(RepoMock $repo)
    {
        self::$repo = $repo;
    }

    public static function getRepoStatic()
    {
        return self::$repo;
    }


    public $id;
    public $name = 'test';
}
