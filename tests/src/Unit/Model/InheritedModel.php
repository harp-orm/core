<?php

namespace Harp\Core\Test\Unit\Model;

use Harp\Core\Model\AbstractModel;
use Harp\Core\Model\InheritedTrait;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class InheritedModel extends AbstractModel
{
    const REPO = 'Harp\Core\Test\Unit\Model\InheritedRepo';

    use InheritedTrait;
}
