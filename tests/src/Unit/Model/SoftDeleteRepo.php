<?php

namespace Harp\Core\Test\Unit\Model;

use Harp\Core\Repo\AbstractRepo;

class SoftDeleteRepo extends AbstractRepo
{
    private static $instance;

    /**
     * @return User
     */
    public static function get()
    {
        if (! self::$instance) {
            self::$instance = new SoftDeleteRepo(__NAMESPACE__.'\SoftDeleteModel', 'Model.json');
        }

        return self::$instance;
    }

    public function initialize()
    {
        $this
            ->setSoftDelete(true);
    }
}
