<?php

namespace Harp\Core\Test\Unit\Save;

use Harp\Core\Save\AbstractFind;
use BadMethodCallException;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Find extends AbstractFind
{
    public function where($property, $value)
    {
        throw new BadMethodCallException('Test Rel: cannot call where');
    }

    public function whereNot($property, $value)
    {
        throw new BadMethodCallException('Test Rel: cannot call whereNot');
    }

    public function whereIn($property, array $value)
    {
        throw new BadMethodCallException('Test Rel: cannot call whereIn');
    }

    public function clearWhere()
    {
        throw new BadMethodCallException('Test Rel: cannot call offset');
    }

    public function limit($limit)
    {
        throw new BadMethodCallException('Test Rel: cannot call limit');
    }

    public function clearLimit()
    {
        throw new BadMethodCallException('Test Rel: cannot call offset');
    }

    public function offset($offset)
    {
        throw new BadMethodCallException('Test Rel: cannot call offset');
    }

    public function clearOffset()
    {
        throw new BadMethodCallException('Test Rel: cannot call offset');
    }

    public function execute()
    {
        throw new BadMethodCallException('Test Rel: cannot call execute');
    }
}
