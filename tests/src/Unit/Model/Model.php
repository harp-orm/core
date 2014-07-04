<?php

namespace Harp\Core\Test\Unit\Model;

use Harp\Core\Test\Model\AbstractTestModel;
use Harp\Core\Repo\AbstractRepo;
use Harp\Core\Repo\Event;
use Harp\Validate\Assert\Present;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Model extends AbstractTestModel
{
    public static function initialize(AbstractRepo $repo)
    {
        $repo
            ->addAsserts([
                new Present('name'),
                new Present('other'),
            ])
            ->addEventAfter(Event::CONSTRUCT, function($model){
                $model->afterConstructCalled = true;
            });
    }

    public $id;
    public $name = 'test';
    public $afterConstructCalled = false;

    public function getName()
    {
        return $this->name;
    }
}
