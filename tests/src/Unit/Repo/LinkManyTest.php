<?php

namespace CL\LunaCore\Test\Unit\Repo;

use CL\LunaCore\Repo\LinkMany;
use CL\LunaCore\Util\Objects;
use SplObjectStorage;

class LinkManyTest extends AbstractRepoTestCase
{
    /**
     * @covers CL\LunaCore\Repo\LinkMany::__construct
     * @covers CL\LunaCore\Repo\LinkMany::all
     * @covers CL\LunaCore\Repo\LinkMany::getOriginals
     */
    public function testConstruct()
    {
        $rel = $this->getRelMany();
        $models = [new Model(), new Model()];

        $link = new LinkMany($rel, $models);

        $this->assertSame($rel, $link->getRel());
        $this->assertSame($models, Objects::toArray($link->all()));
        $this->assertSame($models, Objects::toArray($link->getOriginals()));
    }

    /**
     * @covers CL\LunaCore\Repo\LinkMany::clear
     */
    public function testClear()
    {
        $link = $this->getLinkMany();

        $link->clear();

        $this->assertCount(0, $link);
    }

    /**
     * @covers CL\LunaCore\Repo\LinkMany::set
     */
    public function testSet()
    {
        $link = $this->getLinkMany();

        $expected = [new Model(), new Model()];

        $link->set($expected);

        $this->assertSame($expected, $link->asArray());
    }

    /**
     * @covers CL\LunaCore\Repo\LinkMany::add
     */
    public function testAdd()
    {
        $link = $this->getLinkMany();

        $model = new Model();
        $expected = array_merge($link->asArray(), [$model]);

        $link->add($model);

        $this->assertSame($expected, $link->asArray());
    }

    /**
     * @covers CL\LunaCore\Repo\LinkMany::remove
     */
    public function testRemove()
    {
        $link = $this->getLinkMany();
        $model = $link->rewind()->current();

        $link->remove($model);

        $this->assertCount(1, $link->all());
        $this->assertFalse($link->all()->contains($model));

        $link->remove($model);

        $this->assertCount(1, $link->all(), 'Should be the same result');
        $this->assertFalse($link->all()->contains($model), 'Should be the same result');
    }

    /**
     * @covers CL\LunaCore\Repo\LinkMany::isEmpty
     */
    public function testIsEmpty()
    {
        $link = $this->getLinkMany();

        $this->assertFalse($link->isEmpty());

        $emptyLink = new LinkMany($this->getRelMany(), []);

        $this->assertTrue($emptyLink->isEmpty());
    }

    /**
     * @covers CL\LunaCore\Repo\LinkMany::has
     */
    public function testHas()
    {
        $link = $this->getLinkMany();

        $model = $link->rewind()->current();
        $otherModel = new Model();

        $this->assertFalse($link->has($otherModel));
        $this->assertTrue($link->has($model));
    }

    /**
     * @covers CL\LunaCore\Repo\LinkMany::hasId
     */
    public function testHasId()
    {
        $link = $this->getLinkMany();

        $model = $link->rewind()->current();

        $this->assertFalse($link->hasId(10000));
        $this->assertTrue($link->hasId($model->getId()));
    }

    /**
     * @covers CL\LunaCore\Repo\LinkMany::asArray
     */
    public function testAsArray()
    {
        $models = [new Model(), new Model()];
        $link = new LinkMany($this->getRelMany(), $models);

        $array = $link->asArray();

        $this->assertSame($models, $array);
    }

    /**
     * @covers CL\LunaCore\Repo\LinkMany::getOriginals
     */
    public function testGetOriginals()
    {
        $link = $this->getLinkMany();
        $originals = $link->getOriginals();

        $link->set([new Model()]);

        $this->assertSame($originals, $link->getOriginals());
    }

    /**
     * @covers CL\LunaCore\Repo\LinkMany::getOriginalIds
     */
    public function testGetOriginalIds()
    {
        $link = $this->getLinkMany();
        $link->set([new Model()]);
        $this->assertSame([10, 20], $link->getOriginalIds());
    }

    /**
     * @covers CL\LunaCore\Repo\LinkMany::getIds
     */
    public function testGetIds()
    {
        $link = $this->getLinkMany();
        $this->assertSame([10, 20], $link->getIds());
        $link->set([new Model(['id' => 5])]);
        $this->assertSame([5], $link->getIds());
    }

    /**
     * @covers CL\LunaCore\Repo\LinkMany::getAdded
     */
    public function testGetAdded()
    {
        $link = $this->getLinkMany();
        $model1 = new Model();
        $model2 = new Model();

        $link->all()->attach($model1);
        $link->all()->attach($model2);

        $added = $link->getAdded();

        $this->assertInstanceOf('SplObjectStorage', $added);
        $this->assertSame([$model1, $model2], Objects::toArray($added));
    }

    /**
     * @covers CL\LunaCore\Repo\LinkMany::getAddedIds
     */
    public function testGetAddedIds()
    {
        $link = $this->getLinkMany();
        $model1 = new Model(['id' => 2]);
        $model2 = new Model(['id' => 4]);

        $link->all()->attach($model1);
        $link->all()->attach($model2);

        $ids = $link->getAddedIds();

        $this->assertSame([2, 4], $ids);
    }

    /**
     * @covers CL\LunaCore\Repo\LinkMany::getRemoved
     */
    public function testGetRemoved()
    {
        $link = $this->getLinkMany();
        $items = Objects::toArray($link->all());

        $link->all()->attach(new Model());
        $link->all()->offsetUnset($items[0]);
        $link->all()->offsetUnset($items[1]);

        $removed = $link->getRemoved();

        $this->assertInstanceOf('SplObjectStorage', $removed);
        $this->assertSame($items, Objects::toArray($removed));
    }

    /**
     * @covers CL\LunaCore\Repo\LinkMany::getRemovedIds
     */
    public function testGetRemovedIds()
    {
        $link = $this->getLinkMany();
        $items = $link->asArray();

        $link->all()->attach(new Model());
        $link->all()->offsetUnset($items[0]);
        $link->all()->offsetUnset($items[1]);

        $ids = $link->getRemovedIds();

        $this->assertSame([10, 20], $ids);
    }

    /**
     * @covers CL\LunaCore\Repo\LinkMany::getCurrentAndOriginal
     */
    public function testGetCurrentAndOriginal()
    {
        $link = $this->getLinkMany();

        $items = Objects::toArray($link->all());
        $model1 = new Model();
        $model2 = new Model();

        $link->all()->attach($model1);
        $link->all()->attach($model2);
        $link->all()->offsetUnset($items[0]);

        $result = $link->getCurrentAndOriginal();

        $this->assertInstanceOf('SplObjectStorage', $result);

        $this->assertCount(4, $result);
        $this->assertTrue($result->contains($model1));
        $this->assertTrue($result->contains($model2));
        $this->assertTrue($result->contains($items[0]));
        $this->assertTrue($result->contains($items[1]));
    }

    /**
     * @covers CL\LunaCore\Repo\LinkMany::getFirst
     */
    public function testGetFirst()
    {
        $link = $this->getLinkMany();
        $items = Objects::toArray($link->all());
        $first = $link->getFirst();

        $this->assertSame($items[0], $first);

        $link = new LinkMany($this->getRelMany(), []);
        $first = $link->getFirst();

        $this->assertInstanceof('CL\LunaCore\Test\Unit\Repo\Model', $first);
        $this->assertTrue($first->isVoid());
    }

    /**
     * @covers CL\LunaCore\Repo\LinkMany::count
     */
    public function testCountable()
    {
        $link = $this->getLinkMany();
        $this->assertCount(2, $link);
        $link->all()->attach(new Model());
        $this->assertCount(3, $link);
    }

    /**
     * @covers CL\LunaCore\Repo\LinkMany::current
     * @covers CL\LunaCore\Repo\LinkMany::key
     * @covers CL\LunaCore\Repo\LinkMany::next
     * @covers CL\LunaCore\Repo\LinkMany::rewind
     * @covers CL\LunaCore\Repo\LinkMany::valid
     */
    public function testIterator()
    {
        $link = $this->getLinkMany();
        $expected = Objects::toArray($link->all());

        foreach ($link as $item) {
            $this->assertSame(current($expected), $item);
            next($expected);
        }
    }
}
