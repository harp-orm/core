<?php

namespace CL\LunaCore\Test\Unit\Model;

use CL\LunaCore\Repo\AbstractRepo;
use CL\Carpo\Assert\Present;

class Repo extends AbstractRepo
{
    private static $instance;

    /**
     * @return User
     */
    public static function get()
    {
        if (! self::$instance) {
            self::$instance = new Repo(Model::class, 'Model.json');
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
