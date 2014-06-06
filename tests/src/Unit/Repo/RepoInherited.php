<?php

namespace Harp\Core\Test\Unit\Repo;

class RepoInherited extends Repo
{
    public static function newInstance()
    {
        return new RepoInherited(__NAMESPACE__.'\RepoInherited');
    }

    public function initialize()
    {
        parent::initialize();

        $this->setRootRepo(Repo::get());
    }
}
