<?php

namespace CL\LunaCore\Test\Unit\Repo;

use CL\LunaCore\Rel\AbstractRelMany;
use CL\LunaCore\Rel\DeleteManyInterface;
use CL\LunaCore\Rel\InsertManyInterface;
use CL\LunaCore\Rel\UpdateManyInterface;
use CL\LunaCore\Model\AbstractModel;
use CL\LunaCore\Model\Models;
use CL\LunaCore\Repo\LinkMany;
use BadMethodCallException;

class RelMany extends AbstractRelMany implements DeleteManyInterface, InsertManyInterface, UpdateManyInterface
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

    public function delete(AbstractModel $model, LinkMany $link)
    {
        throw new BadMethodCallException('Test Rel: cannot call delete');
    }

    public function insert(AbstractModel $model, LinkMany $link)
    {
        throw new BadMethodCallException('Test Rel: cannot call insert');
    }

    public function update(AbstractModel $model, LinkMany $link)
    {
        throw new BadMethodCallException('Test Rel: cannot call update');
    }
}
