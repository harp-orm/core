<?php

namespace Harp\Core\Rel;

use Harp\Core\Repo\LinkMany;
use Harp\Core\Model\AbstractModel;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
interface InsertManyInterface
{
    public function insert(AbstractModel $model, LinkMany $link);
}
