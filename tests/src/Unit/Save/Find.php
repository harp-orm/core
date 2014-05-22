<?php

namespace CL\LunaCore\Test\Unit\Save;

use CL\LunaCore\Save\AbstractFind;
use BadMethodCallException;

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
