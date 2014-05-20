<?php

namespace CL\LunaCore\Test\Unit\Model;

use CL\LunaCore\Repo\AbstractRepo;

class SoftDeleteRepo extends AbstractRepo
{
    private static $instance;

    /**
     * @return User
     */
    public static function get()
    {
        if (! self::$instance) {
            self::$instance = new SoftDeleteRepo(SoftDeleteModel::class, 'Model.json');
        }

        return self::$instance;
    }

    public function initialize()
    {
        $this
            ->setSoftDelete(true);
    }
}
