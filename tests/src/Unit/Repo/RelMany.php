<?php

namespace Harp\Core\Test\Unit\Repo;

use Harp\Core\Rel\AbstractRelMany;
use Harp\Core\Rel\DeleteManyInterface;
use Harp\Core\Rel\InsertManyInterface;
use Harp\Core\Rel\UpdateManyInterface;
use Harp\Core\Model\AbstractModel;
use Harp\Core\Model\Models;
use Harp\Core\Repo\LinkMany;
use BadMethodCallException;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
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

    public function delete(LinkMany $link)
    {
        throw new BadMethodCallException('Test Rel: cannot call delete');
    }

    public function insert(LinkMany $link)
    {
        throw new BadMethodCallException('Test Rel: cannot call insert');
    }

    public function update(LinkMany $link)
    {
        throw new BadMethodCallException('Test Rel: cannot call update');
    }
}
