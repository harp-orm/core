<?php

namespace Harp\Core\Rel;

use Harp\Core\Repo\LinkOne;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
interface DeleteOneInterface
{
    public function delete(LinkOne $link);
}
