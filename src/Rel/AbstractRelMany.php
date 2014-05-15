<?php

namespace CL\LunaCore\Rel;

use CL\LunaCore\Model\AbstractModel;
use CL\LunaCore\Repo\LinkMany;
use CL\LunaCore\Util\Objects;
use SplObjectStorage;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractRelMany extends AbstractRel
{
    public function newLink(array $foreignModels)
    {
        foreach ($foreignModels as & $model) {
            $model = $model->getRepo()->getIdentityMap()->get($model);
        }

        return new LinkMany($this, $foreignModels);
    }

    public function newEmptyLink()
    {
        return new LinkMany($this, []);
    }

    public function linkToForeign(array $models, array $foreign)
    {
        return Objects::groupCombineArrays($models, $foreign, function ($model, $foreign) {
            return $this->areLinked($model, $foreign);
        });
    }

    public function newLinkFrom(AbstractModel $model, SplObjectStorage $links)
    {
        if ($links->contains($model)) {
            return $this->newLink($links[$model]);
        } else {
            return $this->newEmptyLink();
        }
    }
}
