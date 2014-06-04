<?php

namespace Harp\Core\Repo;

use Harp\Core\Rel\AbstractRel;
use Harp\Core\Model\AbstractModel;
use Harp\Core\Model\Models;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractLink
{
    private $rel;
    private $model;

    public function __construct(AbstractModel $model, AbstractRel $rel)
    {
        $this->rel = $rel;
        $this->model = $model;
    }

    public function getRel()
    {
        return $this->rel;
    }

    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return Models|null
     */
    abstract public function delete();

    /**
     * @return Models|null
     */
    abstract public function insert();

    /**
     * @return Models|null
     */
    abstract public function update();

    /**
     * @return Models
     */
    abstract public function getCurrentAndOriginal();

    /**
     * @return boolean
     */
    abstract public function isChanged();

    abstract public function clear();

    abstract public function get();

    abstract public function getOriginal();
}
