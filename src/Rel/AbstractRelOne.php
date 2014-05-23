<?php

namespace CL\LunaCore\Rel;

use CL\LunaCore\Model\AbstractModel;
use CL\LunaCore\Model\Models;
use CL\LunaCore\Repo\LinkOne;

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
        return new LinkOne($this, $this->getForeignRepo()->newVoidModel());
    }

    public function newLinkFrom(AbstractModel $model, array $linked)
    {
        if (empty($linked)) {
            return $this->newEmptyLink();
        } else {
            return $this->newLink(reset($linked));
        }
    }
}
