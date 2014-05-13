<?php

namespace CL\LunaCore\Test\Rel;

use CL\LunaCore\Test\Repo\AbstractTestRepo;
use CL\LunaCore\Rel\UpdateInterface;
use CL\LunaCore\Rel\AbstractRelMany;
use CL\LunaCore\Model\AbstractModel;
use CL\LunaCore\Repo\AbstractLink;
use CL\LunaCore\Util\Arr;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Many extends AbstractRelMany implements UpdateInterface
{
    private $key;

    public function __construct($name, AbstractTestRepo $repo, AbstractTestRepo $foreignRepo, array $options = array())
    {
        $this->key = lcfirst($repo->getName()).'Id';

        parent::__construct($name, $repo, $foreignRepo, $options);
    }

    public function areLinked(AbstractModel $model, AbstractModel $foreign)
    {
        return $model->getId() == $foreign->{$this->key};
    }

    public function hasForeign(array $models)
    {
        return ! empty(Arr::extractUnique($models, 'id'));
    }

    public function loadForeign(array $models)
    {
        return $this->getForeignRepo()
            ->findByKey($this->key, Arr::extractUnique($models, 'id'));
    }

    public function update(AbstractModel $model, AbstractLink $link)
    {
        foreach ($link->getAdded() as $added) {
            $added->{$this->key} = $model->getId();
        }

        foreach ($link->getRemoved() as $added) {
            $added->{$this->key} = null;
        }
    }
}
