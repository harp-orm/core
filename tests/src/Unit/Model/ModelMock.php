<?php

namespace Harp\Core\Test\Unit\Model;

use Harp\Core\Model\AbstractModel;

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
