<?php

namespace CL\LunaCore\Rel;

use CL\LunaCore\Model\AbstractModel;
use CL\LunaCore\Repo\LinkMany;
use CL\Util\Objects;
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

    public function newLinkFrom(AbstractModel $model, array $linked)
    {
        if (empty($linked)) {
            return $this->newEmptyLink();
        } else {
            return $this->newLink($linked);
        }
    }
}
