<?php

namespace Harp\Core\Test\Unit\Model;

use Harp\Core\Test\Model\AbstractTestModel;
use Harp\Core\Model\SoftDeleteTrait;
use Harp\Core\Repo\AbstractRepo;
use Harp\Validate\Assert\Present;
use Harp\Core\Repo\Event;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class SoftDeleteModel extends AbstractTestModel
{
    const REPO = 'Harp\Core\Test\Unit\Model\SoftDeleteRepo';

    use SoftDeleteTrait;

    public static function initialize(AbstractRepo $repo)
    {
        SoftDeleteTrait::initialize($repo);

        $repo
            ->addAsserts([
                new Present('name'),
                new Present('other'),
            ])
            ->addEventAfter(Event::CONSTRUCT, function($model){
                $model->afterConstructCalled = true;
            });
    }
}
