<?php

namespace Harp\Core\Rel;

use Harp\Core\Repo\LinkOne;

/**
 * This interface is used by relations that will delete foreign models
 * (e.g. when dissaciated with the parent model)
 *
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
interface DeleteOneInterface
{
    /**
     * After deleting the models should return a collection of models, that have been deleted
     *
     * @param  LinkOne $link
     * @return \Harp\Core\Model\Models|null
     */
    public function delete(LinkOne $link);
}
