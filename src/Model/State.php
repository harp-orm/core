<?php

namespace Harp\Core\Model;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class State
{
    const PENDING = 1;
    const DELETED = 2;
    const SAVED = 4;
    const VOID = 8;
}
