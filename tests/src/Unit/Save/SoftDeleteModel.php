<?php

namespace Harp\Core\Test\Unit\Save;

use Harp\Core\Model\AbstractModel;
use Harp\Core\Model\SoftDeleteTrait;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class SoftDeleteModel extends AbstractModel
{
    const REPO = 'Harp\Core\Test\Unit\Save\SoftDeleteRepo';

    use SoftDeleteTrait;

    public $id;
    public $name = 'test';
    public $repo;

    public function getRepo()
    {
        return $this->repo ?: parent::getRepo();
    }
}
