<?php

namespace CL\LunaCore\Test\Unit\Repo;

use CL\LunaCore\Repo\EventListeners;
use CL\LunaCore\Repo\Event;

class EventListenersTest extends AbstractRepoTestCase
{
    /**
     * @covers CL\LunaCore\Repo\EventListeners::dispatchEvent
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
     * @covers CL\LunaCore\Repo\EventListeners::getBefore
     * @covers CL\LunaCore\Repo\EventListeners::addBefore
     * @covers CL\LunaCore\Repo\EventListeners::hasBeforeEvent
     * @covers CL\LunaCore\Repo\EventListeners::dispatchBeforeEvent
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
     * @covers CL\LunaCore\Repo\EventListeners::getAfter
     * @covers CL\LunaCore\Repo\EventListeners::addAfter
     * @covers CL\LunaCore\Repo\EventListeners::hasAfterEvent
     * @covers CL\LunaCore\Repo\EventListeners::dispatchAfterEvent
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
