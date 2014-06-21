<?php

namespace Harp\Core\Rel;

use Harp\Core\Model\AbstractModel;
use Harp\Core\Repo\LinkOne;

/**
 * Represents linking of one model to another model. A basis a "belongs to" association.
 * A "one" relation will always return a LinkOne result with a model. If a model cannot be loaded,
 * a "void model will be created for the foreign repo.
 *
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractRelOne extends AbstractRel
{
    /**
     * Return a LinkOne based on the linked model. Only the first linked model is considered,
     * generally this array should conain only one model anyway.
     * (its only an array for consistency with RelMany).
     *
     * If no model is found, will the link will hold a void model from the foreign repo.
     *
     * @param  AbstractModel $model
     * @param  array         $linked
     * @return LinkOne
     */
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
