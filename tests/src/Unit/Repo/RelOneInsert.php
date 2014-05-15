<?php

namespace CL\LunaCore\Test\Unit\Repo;

use CL\LunaCore\Rel\AbstractRelOne;
use CL\LunaCore\Rel\InsertInterface;
use CL\LunaCore\Model\AbstractModel;
use CL\LunaCore\Repo\AbstractLink;

class RelOneInsert extends AbstractRelOne implements InsertInterface
{
    public function areLinked(AbstractModel $model, AbstractModel $foreign)
    {
        throw new BadMethodCallException('Test Rel: cannot call areLinked');
    }

    public function hasForeign(array $models)
    {
        throw new BadMethodCallException('Test Rel: cannot call hasForeign');
    }

    public function loadForeign(array $models)
    {
        throw new BadMethodCallException('Test Rel: cannot call loadForeign');
    }

    public function insert(AbstractModel $model, AbstractLink $link)
    {
        throw new BadMethodCallException('Test Rel: cannot call update');
    }
}
