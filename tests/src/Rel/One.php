<?php

namespace Harp\Core\Test\Rel;

use Harp\Core\Test\Repo\TestRepo;
use Harp\Core\Rel\AbstractRelOne;
use Harp\Core\Rel\UpdateOneInterface;
use Harp\Core\Model\AbstractModel;
use Harp\Core\Model\Models;
use Harp\Core\Repo\LinkOne;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class One extends AbstractRelOne implements UpdateOneInterface
{
    private $key;

    public function __construct($name, TestRepo $repo, TestRepo $foreignRepo, array $options = array())
    {
        $this->key = lcfirst($foreignRepo->getName()).'Id';

        parent::__construct($name, $repo, $foreignRepo, $options);
    }

    public function areLinked(AbstractModel $model, AbstractModel $foreign)
    {
        return $model->{$this->key} == $foreign->getId();
    }

    public function hasForeign(Models $models)
    {
        return ! $models->isEmptyProperty($this->key);
    }

    public function loadForeign(Models $models, $flags = null)
    {
        $keys = $models->pluckPropertyUnique($this->key);

        return $this->getForeignRepo()
            ->findAll()
            ->whereIn('id', $keys)
            ->setFlags($flags)
            ->loadRaw();
    }

    public function update(LinkOne $link)
    {
        $link->getModel()->{$this->key} = $link->get()->getId();
    }
}
