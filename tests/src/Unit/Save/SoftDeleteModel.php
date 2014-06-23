<?php

namespace Harp\Core\Test\Unit\Save;

use Harp\Core\Model\AbstractModel;
use Harp\Core\Model\SoftDeleteTrait;

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
