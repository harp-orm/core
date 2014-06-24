<?php

namespace Harp\Core\Test\Unit\Model;

use Harp\Core\Repo\Event;
use Harp\Core\Repo\AbstractRepo;
use Harp\Validate\Assert\Present;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Repo extends AbstractRepo
{
    public function initialize()
    {
        $this
            ->setModelClass(__NAMESPACE__.'\Model')
            ->addAsserts([
                new Present('name'),
                new Present('other'),
            ])
            ->addEventAfter(Event::CONSTRUCT, function($model){
                $model->afterConstructCalled = true;
            });
    }
}
