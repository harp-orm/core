<?php

namespace Harp\Core\Test\Unit\Rel;

use Harp\Core\Model\AbstractModel;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Model extends AbstractModel
{
    const REPO = 'Harp\Core\Test\Unit\Rel\Repo';

    public $id;
    public $name = 'test';
}
