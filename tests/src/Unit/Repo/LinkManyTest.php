<?php

namespace CL\LunaCore\Test\Unit\Repo;

use CL\LunaCore\Repo\LinkMany;
use CL\LunaCore\Model\Models;

/**
 * @coversDefaultClass CL\LunaCore\Repo\LinkMany
 */
class LinkManyTest extends AbstractRepoTestCase
{
    /**
     * @covers ::__construct
     * @covers ::get
     * @covers ::getRel
     * @covers ::getOriginal
     */
    public function testConstruct()
    {
        $rel = $this->getRelMany();
        $models = [new Model(), new Model()];

        $link = new LinkMany($rel, $models);

        $this->assertSame($rel, $link->getRel());
        $this->assertSame($models, $link->get()->toArray());
        $this->assertSame($models, $link->getOriginal()->toArray());
    }

    /**
     * @covers ::clear
     */
    public function testClear()
    {
        $link = $this->getLinkMany();

        $link->clear();

        $this->assertCount(0, $link);
    }


    /**
     * @covers ::delete
     */
    public function testDelete()
    {
        $models = [new Model()];

        $rel = $this->getMock(
            __NAMESPACE__.'\RelMany',
            ['delete'],
            ['test', new Repo(__NAMESPACE__.'\Model'), new Repo(__NAMESPACE__.'\Model')]
        );

        $link = new LinkMany($rel, $models);
        $model = new Model();
        $expected = new Models();

        $rel
            ->expects($this->once())
            ->method('delete')
            ->with($this->identicalTo($model), $this->identicalTo($link))
            ->will($this->returnValue($expected));

        $result = $link->delete($model);
        $this->assertSame($expected, $result);
    }

    /**
     * @covers ::insert
     */
    public function testInsert()
    {
        $rel = $this->getMock(
            __NAMESPACE__.'\RelMany',
            ['insert'],
            ['test', new Repo(__NAMESPACE__.'\Model'), new Repo(__NAMESPACE__.'\Model')]
        );

        $link = new LinkMany($rel, [new Model()]);
        $model = new Model();
        $expected = new Models();

        $rel
            ->expects($this->once())
            ->method('insert')
            ->with($this->identicalTo($model), $this->identicalTo($link))
            ->will($this->returnValue($expected));

        $reuslt = $link->insert($model);
        $this->assertSame($expected, $reuslt);
    }

    /**
     * @covers ::update
     */
    public function testUpdate()
    {
        $rel = $this->getMock(
            __NAMESPACE__.'\RelMany',
            ['update'],
            ['test', new Repo(__NAMESPACE__.'\Model'), new Repo(__NAMESPACE__.'\Model')]
        );

        $link = new LinkMany($rel, [new Model()]);
        $model = new Model();
        $models = new Models();

        $rel
            ->expects($this->once())
            ->method('update')
            ->with($this->identicalTo($model), $this->identicalTo($link))
            ->will($this->returnValue($models));

        $link->update($model);
    }

    /**
     * @covers ::addArray
     */
    public function testAddArray()
    {
        $link = $this->getLinkMany();

        $model1 = new Model();
        $model2 = new Model();
        $expected = array_merge($link->toArray(), [$model1, $model2]);

        $link->addArray([$model1, $model2]);

        $this->assertSame($expected, $link->toArray());
    }

    /**
     * @covers ::add
     */
    public function testAdd()
    {
        $link = $this->getLinkMany();

        $model = new Model();
        $expected = array_merge($link->toArray(), [$model]);

        $link->add($model);

        $this->assertSame($expected, $link->toArray());
    }

    /**
     * @covers ::isChanged
     */
    public function testIsChanged()
    {
        $link = $this->getLinkMany();

        $this->assertFalse($link->isChanged());

        $link->add($link->getFirst());
        $this->assertFalse($link->isChanged());

        $model = new Model();
        $link->add($model);
        $this->assertTrue($link->isChanged());
    }

    /**
     * @covers ::remove
     */
    public function testRemove()
    {
        $link = $this->getLinkMany();
        $model = $link->getFirst();

        $link->remove($model);

        $this->assertCount(1, $link->get());
        $this->assertFalse($link->get()->has($model));

        $link->remove($model);

        $this->assertCount(1, $link->get(), 'Should be the same result');
        $this->assertFalse($link->get()->has($model), 'Should be the same result');
    }

    /**
     * @covers ::isEmpty
     */
    public function testIsEmpty()
    {
        $link = $this->getLinkMany();

        $this->assertFalse($link->isEmpty());

        $emptyLink = new LinkMany($this->getRelMany(), []);

        $this->assertTrue($emptyLink->isEmpty());
    }

    /**
     * @covers ::has
     */
    public function testHas()
    {
        $link = $this->getLinkMany();

        $model = $link->getFirst();
        $otherModel = new Model();

        $this->assertFalse($link->has($otherModel));
        $this->assertTrue($link->has($model));
    }

    /**
     * @covers ::toArray
     */
    public function testToArray()
    {
        $models = [new Model(), new Model()];
        $link = new LinkMany($this->getRelMany(), $models);

        $array = $link->toArray();

        $this->assertSame($models, $array);
    }

    /**
     * @covers ::getOriginal
     */
    public function testGetOriginal()
    {
        $link = $this->getLinkMany();
        $originals = $link->getOriginal();

        $link->add(new Model());

        $this->assertSame($originals, $link->getOriginal());
    }

    /**
     * @covers ::getAdded
     */
    public function testGetAdded()
    {
        $link = $this->getLinkMany();
        $model1 = new Model();
        $model2 = new Model();

        $link->add($model1);
        $link->add($model2);

        $added = $link->getAdded();

        $this->assertInstanceOf('CL\LunaCore\Model\Models', $added);
        $this->assertSame([$model1, $model2], $added->toArray());
    }

    /**
     * @covers ::getRemoved
     */
    public function testGetRemoved()
    {
        $link = $this->getLinkMany();
        $items = $link->toArray();

        $link
            ->add(new Model())
            ->remove($items[0])
            ->remove($items[1]);

        $removed = $link->getRemoved();

        $this->assertInstanceOf('CL\LunaCore\Model\Models', $removed);
        $this->assertSame($items, $removed->toArray());
    }

    /**
     * @covers ::getCurrentAndOriginal
     */
    public function testGetCurrentAndOriginal()
    {
        $link = $this->getLinkMany();

        $items = $link->toArray();
        $model1 = new Model();
        $model2 = new Model();

        $link
            ->add($model1)
            ->add($model2)
            ->remove($items[0]);

        $result = $link->getCurrentAndOriginal();

        $this->assertInstanceOf('CL\LunaCore\Model\Models', $result);

        $this->assertCount(4, $result);
        $this->assertTrue($result->has($model1));
        $this->assertTrue($result->has($model2));
        $this->assertTrue($result->has($items[0]));
        $this->assertTrue($result->has($items[1]));
    }

    /**
     * @covers ::getFirst
     */
    public function testGetFirst()
    {
        $link = $this->getLinkMany();
        $item = $link->get()->getFirst();

        $this->assertSame($item, $link->getFirst());

        $link = new LinkMany($this->getRelMany(), []);
        $first = $link->getFirst();

        $this->assertInstanceof(__NAMESPACE__.'\Model', $first);
        $this->assertTrue($first->isVoid());
    }

    /**
     * @covers ::count
     */
    public function testCountable()
    {
        $link = $this->getLinkMany();
        $this->assertCount(2, $link);
        $link->add(new Model());
        $this->assertCount(3, $link);
    }

    /**
     * @covers ::current
     * @covers ::key
     * @covers ::next
     * @covers ::rewind
     * @covers ::valid
     */
    public function testIterator()
    {
        $link = $this->getLinkMany();
        $expected = $link->toArray();

        $key = $link->key();

        foreach ($link as $i => $item) {
            $this->assertSame(current($expected), $item);
            next($expected);
        }
    }
}
