<?php

namespace CL\LunaCore\Test\Unit\Repo;

use CL\LunaCore\Repo\LinkMany;
use CL\LunaCore\Repo\LinkOne;
use CL\LunaCore\Test\AbstractTestCase;

abstract class AbstractRepoTestCase extends AbstractTestCase
{
    public function getRelMany()
    {
        $repo1 = new Repo(__NAMESPACE__.'\Model');
        $repo2 = new Repo(__NAMESPACE__.'\Model');

        return new RelMany('many', $repo1, $repo2);
    }

    public function getRelOne()
    {
        $repo1 = new Repo(__NAMESPACE__.'\Model');
        $repo2 = new Repo(__NAMESPACE__.'\Model');

        return new RelOne('one', $repo1, $repo2);
    }

    public function getLinkMany()
    {
        $models = [new Model(['id' => 10]), new Model(['id' => 20])];

        return new LinkMany($this->getRelMany(), $models);
    }

    public function getLinkOne()
    {
        return new LinkOne($this->getRelOne(), new Model());
    }
}
