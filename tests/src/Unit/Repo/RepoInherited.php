<?php

namespace Harp\Core\Test\Unit\Repo;

class RepoInherited extends Repo
{
    public function initialize()
    {
        parent::initialize();

        $this
            ->setModelClass(__NAMESPACE__.'\RepoInherited')
            ->setRootRepo(Repo::get());
    }
}
