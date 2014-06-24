<?php

namespace Harp\Core\Test\Unit\Repo;

use Harp\Core\Model\AbstractModel;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Model extends AbstractModel
{
    const REPO = 'Harp\Core\Test\Unit\Repo\Repo';

    public $id;
    public $name = 'test';
}
