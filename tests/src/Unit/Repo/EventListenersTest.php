<?php

namespace Harp\Core\Test\Unit\Repo;

use Harp\Core\Repo\EventListeners;
use Harp\Core\Repo\Event;

/**
 * @coversDefaultClass Harp\Core\Repo\EventListeners
 */
class EventListenersTest extends AbstractRepoTestCase
{
    /**
     * @covers ::dispatchEvent
     */
    public function testDispatchEvent()
    {
        $model = new Model();

        $listeners = [
            Event::SAVE => [
                function ($model) {
                    $model->callbackSave = true;
                }
            ]
        ];

        EventListeners::dispatchEvent($listeners, $model, Event::SAVE);

        $this->assertTrue($model->callbackSave);
    }

    /**
     * @covers ::getBefore
     * @covers ::addBefore
     * @covers ::hasBeforeEvent
     * @covers ::dispatchBeforeEvent
     */
    public function testBefore()
    {
        $model = new Model();
        $listeners = new EventListeners();

        $this->assertEmpty($listeners->getBefore());
        $this->assertFalse($listeners->hasBeforeEvent(Event::INSERT));

        $listeners->addBefore(Event::INSERT, function ($model) {
            $model->callbackCalled = true;
        });

        $this->assertTrue($listeners->hasBeforeEvent(Event::INSERT));

        $listeners->dispatchBeforeEvent($model, Event::INSERT);

        $this->assertTrue($model->callbackCalled);
    }

    /**
     * @covers ::getAfter
     * @covers ::addAfter
     * @covers ::hasAfterEvent
     * @covers ::dispatchAfterEvent
     */
    public function testAfter()
    {
        $model = new Model();
        $listeners = new EventListeners();

        $this->assertEmpty($listeners->getAfter());
        $this->assertFalse($listeners->hasAfterEvent(Event::INSERT));

        $listeners->addAfter(Event::INSERT, function ($model) {
            $model->callbackCalled = true;
        });

        $this->assertTrue($listeners->hasAfterEvent(Event::INSERT));

        $listeners->dispatchAfterEvent($model, Event::INSERT);

        $this->assertTrue($model->callbackCalled);
    }
}
