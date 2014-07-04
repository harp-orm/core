<?php

namespace Harp\Core\Test\Unit\Repo;

use Harp\Core\Model\InheritedTrait;
use Harp\Core\Repo\AbstractRepo;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class ModelInherited extends Model
{
    public static function initialize(AbstractRepo $repo)
    {
        parent::initialize($repo);

        $repo
            ->setRootRepo(Model::getRepo());
    }
}
