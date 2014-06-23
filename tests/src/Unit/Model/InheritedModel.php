<?php

namespace Harp\Core\Test\Unit\Model;

use Harp\Core\Model\AbstractModel;
use Harp\Core\Model\InheritedTrait;

class InheritedModel extends AbstractModel
{
    const REPO = 'Harp\Core\Test\Unit\Model\InheritedRepo';

    use InheritedTrait;
}
