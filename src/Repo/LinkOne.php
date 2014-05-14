<?php

namespace CL\LunaCore\Repo;

use CL\LunaCore\Rel\AbstractRelOne;
use CL\LunaCore\Model\AbstractModel;
use SplObjectStorage;
use Closure;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class LinkOne extends AbstractLink
{
    /**
     * @var AbstractModel
     */
    protected $current;

    /**
     * @var AbstractModel
     */
    protected $original;

    /**
     * @param AbstractRelOne $rel
     * @param AbstractModel  $current
     */
    public function __construct(AbstractRelOne $rel, AbstractModel $current)
    {
        parent::__construct($rel);

        $this->current = $current;
        $this->original = $current;
    }

    /**
     * @param AbstractModel $current
     * @return LinkOne $this
     */
    public function set(AbstractModel $current)
    {
        $this->current = $current;

        return $this;
    }

    /**
     * @return LinkOne $this
     */
    public function clear()
    {
        $this->current->setStateVoid();

        return $this;
    }

    /**
     * @return AbstractModel current
     */
    public function get()
    {
        return $this->current;
    }

    /**
     * @return AbstractModel
     */
    public function getOriginal()
    {
        return $this->original;
    }

    /**
     * @return boolean
     */
    public function isChanged()
    {
        return $this->current !== $this->original;
    }

    /**
     * @return SplObjectStorage
     */
    public function getCurrentAndOriginal()
    {
        $all = new SplObjectStorage();
        $all->attach($this->current);
        $all->attach($this->original);

        return $all;
    }
}
