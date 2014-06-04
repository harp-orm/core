<?php

namespace Harp\Core\Test\Unit\Repo;

use Harp\Core\Repo\LinkMany;
use Harp\Core\Repo\LinkOne;
use Harp\Core\Test\AbstractTestCase;

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

        return new LinkMany(new Model(), $this->getRelMany(), $models);
    }

    public function getLinkOne()
    {
        return new LinkOne(new Model(), $this->getRelOne(), new Model());
    }
}
