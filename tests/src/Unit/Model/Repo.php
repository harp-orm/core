<?php

namespace Harp\Core\Test\Unit\Model;

use Harp\Core\Repo\AbstractRepo;
use Harp\Validate\Assert\Present;

class Repo extends AbstractRepo
{
    private static $instance;

    /**
     * @return User
     */
    public static function get()
    {
        if (! self::$instance) {
            self::$instance = new Repo(__NAMESPACE__.'\Model', 'Model.json');
        }

        return self::$instance;
    }

    public function initialize()
    {
        $this
            ->setInherited(true)
            ->setAsserts([
                new Present('name'),
                new Present('other'),
            ]);
    }
}
