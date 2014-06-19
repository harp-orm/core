<?php

namespace Harp\Core\Test\Unit\Model;

use Harp\Core\Rel\AbstractRelOne;
use Harp\Core\Model\AbstractModel;
use Harp\Core\Model\Models;
use Harp\Core\Repo\LinkOne;
use BadMethodCallException;

class RelOne extends AbstractRelOne
{
    public function areLinked(AbstractModel $model, AbstractModel $foreign)
    {
        throw new BadMethodCallException('Test Rel: cannot call areLinked');
    }

    public function hasForeign(Models $models)
    {
        throw new BadMethodCallException('Test Rel: cannot call hasForeign');
    }

    public function loadForeign(Models $models, $flags = null)
    {
        throw new BadMethodCallException('Test Rel: cannot call loadForeign');
    }
}
