<?php

namespace Harp\Core\Rel;

use Harp\Core\Model\AbstractModel;
use Harp\Core\Repo\LinkMany;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractRelMany extends AbstractRel
{
    public function newLinkFrom(array $linked)
    {
        foreach ($linked as & $model) {
            $model = $model->getRepo()->getIdentityMap()->get($model);
        }

        return new LinkMany($this, $linked);
    }
}
