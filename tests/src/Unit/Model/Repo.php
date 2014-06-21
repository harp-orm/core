<?php

namespace Harp\Core\Test\Unit\Model;

use Harp\Core\Repo\Event;
use Harp\Core\Repo\AbstractRepo;
use Harp\Validate\Assert\Present;

class Repo extends AbstractRepo
{
    public static function newInstance()
    {
        return new Repo(__NAMESPACE__.'\Model', 'Model.json');;
    }

    public function initialize()
    {
        $this
            ->setInherited(true)
            ->addAsserts([
                new Present('name'),
                new Present('other'),
            ])
            ->addEventAfter(Event::CONSTRUCT, function($model){
                $model->afterConstructCalled = true;
            });
    }
}
