<?php

namespace CL\LunaCore\Repo;

use CL\LunaCore\Rel\AbstractRel;

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

    abstract public function getCurrentAndOriginal();
}
