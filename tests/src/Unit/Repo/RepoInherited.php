<?php

namespace Harp\Core\Test\Unit\Repo;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
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
