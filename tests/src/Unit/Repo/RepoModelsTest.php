<?php

namespace CL\LunaCore\Test\Unit\Repo;

use CL\LunaCore\Repo\RepoModels;
use CL\Util\Objects;
use CL\LunaCore\Test\AbstractTestCase;

/**
 * @coversDefaultClass CL\LunaCore\Repo\RepoModels
 */
class ModelsTest extends AbstractTestCase
{
    /**
     * @covers ::__construct
     * @covers ::getRepo
     */
    public function testConstruct()
    {
        $source = [new Model(), new Model()];
        $repo = Repo::get();

        $models = new RepoModels($repo, $source);

        $this->assertSame($repo, $models->getRepo());
        $this->assertSame($source, Objects::toArray($models->all()));
    }

    /**
     * @covers ::getFirst
     */
    public function testGetFirst()
    {
        $model1 = new Model();
        $model2 = new Model();

        $models = new RepoModels(Repo::get(), [$model1, $model2]);

        $this->assertSame($model1, $models->getFirst());

        $models->clear();

        $first = $models->getFirst();

        $this->assertInstanceOf(__NAMESPACE__.'\Model', $first);
        $this->assertTrue($first->isVoid());
    }

    /**
     * @covers ::getNext
     */
    public function testGetNext()
    {
        $model1 = new Model();
        $model2 = new Model();
        $model3 = new Model();

        $models = new RepoModels(Repo::get(), [$model1, $model2, $model3]);

        $models->getFirst();

        $this->assertSame($model2, $models->getNext());
        $this->assertSame($model3, $models->getNext());


        $next = $models->getNext();

        $this->assertInstanceOf(__NAMESPACE__.'\Model', $next);
        $this->assertTrue($next->isVoid());
    }

    /**
     * @covers ::add
     */
    public function testAdd()
    {
        $models = new RepoModels(Repo::get());

        $model = new Model();

        $models->add($model);

        $this->assertSame([$model], Objects::toArray($models->all()));
    }

    /**
     * @covers ::add
     * @expectedException InvalidArgumentException
     */
    public function testAddInvalid()
    {
        $models = new RepoModels(RepoOther::get());

        $models->add(new Model());
    }
}
