<?php

namespace Harp\Core\Rel;

use Harp\Core\Repo\LinkOne;

/**
 * This interface is used by relations that will add new foreign models
 * (e.g. when there is a link "through" model)
 *
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
interface InsertOneInterface
{
    /**
     * Perform logic to preserve the link for newly inserted models.
     * Return a collection of new models
     *
     * @param  LinkOne $link
     * @return \Harp\Core\Model\Models|null
     */
    public function insert(LinkOne $link);
}
