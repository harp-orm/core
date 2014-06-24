<?php

namespace Harp\Core\Repo;

use Harp\Core\Model\Models;
use Harp\Core\Model\AbstractModel;
use InvalidArgumentException;

/**
 * Represnts Models for a specific repo.
 * Will throw exceptions if you try to add models from a different repo.
 * Also getNext() and getFirst() methods will return void models, instead of nulls
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
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

    /**
     * @param AbstractRepo    $repo
     * @param AbstractModel[] $models
     */
    public function __construct(AbstractRepo $repo, array $models = null)
    {
        $this->repo = $repo;

        parent::__construct($models);
    }

    /**
     * @param  AbstractModel           $model
     * @return Models                  $this
     * @throws InvalidArgumentExtepion If $model not part of the repo
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
     * If model doesn't exist, return a void model
     *
     * @return AbstractModel
     */
    public function getFirst()
    {
        return parent::getFirst() ?: $this->getRepo()->newVoidModel();
    }

    /**
     * If model doesn't exist, return a void model
     *
     * @return AbstractModel
     */
    public function getNext()
    {
        return parent::getNext() ?: $this->getRepo()->newVoidModel();
    }
}
