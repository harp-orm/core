<?php

namespace CL\LunaCore\Test\Unit\Repo;

use CL\LunaCore\Rel\AbstractRelOne;
use CL\LunaCore\Rel\DeleteInterface;
use CL\LunaCore\Model\AbstractModel;
use CL\LunaCore\Model\Models;
use CL\LunaCore\Repo\AbstractLink;

class RelOneDelete extends AbstractRelOne implements DeleteInterface
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

    public function delete(AbstractModel $model, AbstractLink $link)
    {
        throw new BadMethodCallException('Test Rel: cannot call update');
    }
}
