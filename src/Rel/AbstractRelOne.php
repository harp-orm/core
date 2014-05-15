<?php

namespace CL\LunaCore\Rel;

use CL\LunaCore\Model\AbstractModel;
use CL\LunaCore\Repo\LinkOne;
use CL\Util\Objects;
use SplObjectStorage;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractRelOne extends AbstractRel
{
    public function newLink(AbstractModel $model)
    {
        $model = $model->getRepo()->getIdentityMap()->get($model);

        return new LinkOne($this, $model);
    }

    public function newEmptyLink()
    {
        return new LinkOne($this, $this->getForeignRepo()->newVoidInstance());
    }

    public function linkToForeign(array $models, array $foreign)
    {
        return Objects::combineArrays($models, $foreign, function ($model, $foreign) {
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
