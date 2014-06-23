<?php

namespace Harp\Core\Test\Unit\Model;

use Harp\Core\Model\AbstractModel;
use Harp\Core\Model\InheritedTrait;

class InheritedModelBad extends AbstractModel
{
    const REPO = 'Harp\Core\Test\Unit\Model\InheritedRepoBad';

    use InheritedTrait;
}
