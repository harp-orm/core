<?php

namespace Harp\Core\Test\Unit\Repo;

class RepoInherited extends Repo
{
    private static $instance;

    /**
     * @return User
     */
    public static function get()
    {
        if (! self::$instance) {
            self::$instance = new RepoInherited(__NAMESPACE__.'\RepoInherited');
        }

        return self::$instance;
    }

    public function initialize()
    {
        parent::initialize();

        $this->setRootRepo(Repo::get());
    }
}
