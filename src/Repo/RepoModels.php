<?php

namespace CL\LunaCore\Repo;

use CL\Util\Objects;
use CL\LunaCore\Model\Models;
use CL\LunaCore\Model\AbstractModel;
use InvalidArgumentException;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class RepoModels extends Models
{
    public function getRepo()
    {
        return $this->repo;
    }

    public function __construct(AbstractRepo $repo, array $models = null)
    {
        $this->repo = $repo;

        parent::__construct($models);
    }

    /**
     * @param  AbstractModel $model
     * @return Models        $this
     */
    public function add(AbstractModel $model)
    {
        if (! $this->repo->isModel($model)) {
            throw new InvalidArgumentException(
                sprintf('Model must be part of repo %s', $this->repo->getName())
            );
        }

        return parent::add($model);
    }

    /**
     * @return AbstractModel
     */
    public function getFirst()
    {
        return parent::getFirst() ?: $this->getRepo()->newVoidModel();
    }

    /**
     * @return AbstractModel
     */
    public function getNext()
    {
        return parent::getNext() ?: $this->getRepo()->newVoidModel();
    }
}
