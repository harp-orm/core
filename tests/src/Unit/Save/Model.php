<?php

namespace Harp\Core\Test\Unit\Save;

use Harp\Core\Test\Model\AbstractTestModel;
use Harp\Core\Repo\AbstractRepo;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Model extends AbstractTestModel
{
    public static function initialize(AbstractRepo $repo)
    {
        $repo
            ->addRels([
                new RelOne('one', $repo, Model::getRepo()),
                new RelMany('many', $repo, Model::getRepo()),
            ]);
    }

    public $id;
    public $name = 'test';

    public static $repo;

    public static function getRepo()
    {
        return self::$repo ?: parent::getRepo();
    }
}
