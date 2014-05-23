<?php

namespace CL\LunaCore\Rel;

use CL\LunaCore\Repo\LinkOne;
use CL\LunaCore\Model\AbstractModel;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
interface DeleteOneInterface
{
    public function delete(AbstractModel $model, LinkOne $link);
}