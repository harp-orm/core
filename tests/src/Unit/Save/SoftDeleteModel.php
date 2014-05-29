<?php

namespace Harp\Core\Test\Unit\Save;

use Harp\Core\Model\AbstractModel;
use Harp\Core\Model\SoftDeleteTrait;

class SoftDeleteModel extends AbstractModel
{
    use SoftDeleteTrait;

    public $id;
    public $name = 'test';
    public $repo;

    public function getRepo()
    {
        return $this->repo ?: SoftDeleteRepo::get();
    }
}
