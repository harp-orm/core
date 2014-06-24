<?php

namespace Harp\Core\Test\Unit\Repo;

use Harp\Core\Repo\AbstractRepo;
use Harp\Core\Model\Models;
use BadMethodCallException;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class RepoOther2 extends RepoOther
{
    public function initialize()
    {
        parent::initialize();

        $this
            ->setModelClass(__NAMESPACE__.'\ModelOther');
            ->setRootRepo(RepoOther::get());
    }
}
