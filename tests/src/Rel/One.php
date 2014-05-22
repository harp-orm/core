<?php

namespace CL\LunaCore\Test\Rel;

use CL\LunaCore\Test\Repo\AbstractTestRepo;
use CL\LunaCore\Rel\AbstractRelOne;
use CL\LunaCore\Model\AbstractModel;
use CL\LunaCore\Model\Models;
use CL\LunaCore\Repo\LinkOne;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class One extends AbstractRelOne
{
    private $key;

    public function __construct($name, AbstractTestRepo $repo, AbstractTestRepo $foreignRepo, array $options = array())
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
            ->loadRaw($flags);
    }

    public function update(AbstractModel $model, LinkOne $link)
    {
        $model->{$this->key} = $link->get()->getId();
    }
}
