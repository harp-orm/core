<?php

namespace CL\LunaCore\Test\Unit\Repo;

use CL\LunaCore\Rel\AbstractRelOne;
use CL\LunaCore\Rel\DeleteOneInterface;
use CL\LunaCore\Rel\InsertOneInterface;
use CL\LunaCore\Rel\UpdateOneInterface;
use CL\LunaCore\Model\AbstractModel;
use CL\LunaCore\Model\Models;
use CL\LunaCore\Repo\LinkOne;
use BadMethodCallException;

class RelOne extends AbstractRelOne implements DeleteOneInterface, InsertOneInterface, UpdateOneInterface
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

    public function delete(AbstractModel $model, LinkOne $link)
    {
        throw new BadMethodCallException('Test Rel: cannot call delete');
    }

    public function insert(AbstractModel $model, LinkOne $link)
    {
        throw new BadMethodCallException('Test Rel: cannot call insert');
    }

    public function update(AbstractModel $model, LinkOne $link)
    {
        throw new BadMethodCallException('Test Rel: cannot call update');
    }
}
