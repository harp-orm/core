<?php

namespace Harp\Core\Rel;

use Harp\Core\Model\AbstractModel;
use Harp\Core\Repo\LinkOne;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractRelOne extends AbstractRel
{
    public function newLinkFrom(AbstractModel $model, array $linked)
    {
        if (empty($linked)) {
            $foreign = $this->getForeignRepo()->newVoidModel();
        } else {
            $foreign = reset($linked);
            $foreign = $foreign->getRepo()->getIdentityMap()->get($foreign);
        }

        return new LinkOne($model, $this, $foreign);
    }
}
