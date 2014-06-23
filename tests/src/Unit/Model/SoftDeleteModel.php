<?php

namespace Harp\Core\Test\Unit\Model;

use Harp\Core\Model\AbstractModel;
use Harp\Core\Model\SoftDeleteTrait;

class SoftDeleteModel extends AbstractModel
{
    const REPO = 'Harp\Core\Test\Unit\Model\SoftDeleteRepo';

    use SoftDeleteTrait;
}
