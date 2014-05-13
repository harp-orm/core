<?php

namespace CL\LunaCore\Repo;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class NodeEvent
{
    const LOAD = 1;
    const INSERT = 2;
    const UPDATE = 3;
    const DELETE = 4;
    const SAVE = 5;
    const VALIDATE = 6;
}
