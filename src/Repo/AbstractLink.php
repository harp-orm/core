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
    protected $rel;

    public function __construct(AbstractRel $rel)
    {
        $this->rel = $rel;
    }

    public function getRel()
    {
        return $this->rel;
    }

    /**
     * @return Models
     */
    abstract public function delete(AbstractModel $model);

    /**
     * @return Models
     */
    abstract public function insert(AbstractModel $model);

    abstract public function update(AbstractModel $model);

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
