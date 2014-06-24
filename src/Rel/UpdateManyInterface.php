<?php

namespace Harp\Core\Rel;

use Harp\Core\Repo\LinkMany;

/**
 * This interface is used by relations that will modify other models
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
interface UpdateManyInterface
{
    /**
     * Perform logic to preserve the link after the update is done
     * Return a collection of new models
     *
     * @param  LinkMany                     $link
     * @return \Harp\Core\Model\Models|null
     */
    public function update(LinkMany $link);
}
