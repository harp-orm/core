<?php

namespace Harp\Core\Test\Unit\Repo;

class ModelInherited extends Model
{
    public function getRepo()
    {
        return RepoInherited::get();
    }
}
