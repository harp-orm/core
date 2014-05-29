<?php

namespace Harp\Core\Repo;

use Harp\Core\Model\Models;
use Harp\Core\Model\AbstractModel;
use InvalidArgumentException;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class RepoModels extends Models
{
    /**
     * AbstractRepo
     */
    private $repo;

    /**
     * @return AbstractRepo
     */
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
