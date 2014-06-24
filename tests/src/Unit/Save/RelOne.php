<?php

namespace Harp\Core\Test\Unit\Save;

use Harp\Core\Rel\AbstractRelOne;
use Harp\Core\Rel\DeleteOneInterface;
use Harp\Core\Rel\InsertOneInterface;
use Harp\Core\Rel\UpdateOneInterface;
use Harp\Core\Model\AbstractModel;
use Harp\Core\Model\Models;
use Harp\Core\Repo\LinkOne;
use BadMethodCallException;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
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

    public function delete(LinkOne $link)
    {
        throw new BadMethodCallException('Test Rel: cannot call delete');
    }

    public function insert(LinkOne $link)
    {
        throw new BadMethodCallException('Test Rel: cannot call insert');
    }

    public function update(LinkOne $link)
    {
        throw new BadMethodCallException('Test Rel: cannot call update');
    }
}
