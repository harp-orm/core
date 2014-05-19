<?php

namespace CL\LunaCore\Test\Unit\Repo;

use CL\LunaCore\Rel\AbstractRelMany;
use CL\LunaCore\Model\AbstractModel;
use CL\LunaCore\Model\Models;
use CL\LunaCore\Repo\AbstractLink;

class RelMany extends AbstractRelMany
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
