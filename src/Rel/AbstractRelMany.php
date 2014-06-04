<?php

namespace Harp\Core\Rel;

use Harp\Core\Model\AbstractModel;
use Harp\Core\Repo\LinkMany;
use InvalidArgumentException;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractRelMany extends AbstractRel
{
    private $linkClass;

    public function setLinkClass($class)
    {
        if (! is_subclass_of($class, 'Harp\Core\Repo\LinkMany')) {
            throw new InvalidArgumentException(
                sprintf('Class %s must be a subclass of LinkMany', $class)
            );
        }

        $this->linkClass = $class;

        return $this;
    }

    public function getLinkClass()
    {
        return $this->linkClass;
    }

    public function newLinkFrom(AbstractModel $model, array $linked)
    {
        foreach ($linked as & $model) {
            $model = $model->getRepo()->getIdentityMap()->get($model);
        }

        if ($this->linkClass) {
            $class = $this->linkClass;
            return new $class($model, $this, $linked);
        } else {
            return new LinkMany($model, $this, $linked);
        }
    }
}
