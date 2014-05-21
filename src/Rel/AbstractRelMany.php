<?php

namespace CL\LunaCore\Rel;

use CL\LunaCore\Model\AbstractModel;
use CL\LunaCore\Model\Models;
use CL\LunaCore\Repo\LinkMany;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractRelMany extends AbstractRel
{
    public function newLink(array $foreignModels)
    {
        foreach ($foreignModels as & $model) {
            $model = $model->getRepo()->getIdentityMap()->get($model);
        }

        return new LinkMany($this, $foreignModels);
    }

    public function newEmptyLink()
    {
        return new LinkMany($this, []);
    }

    public function newLinkFrom(AbstractModel $model, array $linked)
    {
        if (empty($linked)) {
            return $this->newEmptyLink();
        } else {
            return $this->newLink($linked);
        }
    }

    /**
     * @param  AbstractModel $model
     * @param  LinkMany       $link
     * @return Models
     */
    public function insert(AbstractModel $model, LinkMany $link)
    {
        return new Models();
    }

    /**
     * @param  AbstractModel $model
     * @param  LinkMany       $link
     * @return Models
     */
    public function delete(AbstractModel $model, LinkMany $link)
    {
        return new Models();
    }

    /**
     * @param  AbstractModel $model
     * @param  LinkMany       $link
     */
    public function update(AbstractModel $model, LinkMany $link)
    {
    }
}
