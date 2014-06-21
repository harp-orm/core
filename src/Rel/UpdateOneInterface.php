<?php

namespace Harp\Core\Rel;

use Harp\Core\Repo\LinkOne;

/**
 * This interface is used by relations that will modify other models
 *
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
interface UpdateOneInterface
{
    /**
     * Perform logic to preserve the link after the update is done
     * Return a collection of new models
     *
     * @param  LinkOne $link
     * @return \Harp\Core\Model\Models|null
     */
    public function update(LinkOne $link);
}
