<?php

namespace CL\LunaCore\Test\Unit\Repo;

use CL\LunaCore\Repo\Links;

class LinksTest extends AbstractRepoTestCase
{
    /**
     * @covers CL\LunaCore\Repo\Links::getModel
     * @covers CL\LunaCore\Repo\Links::__construct
     * @covers CL\LunaCore\Repo\Links::all
     */
    public function testConstruct()
    {
        $model = new Model();
        $links = new Links($model);

        $this->assertSame($model, $links->getModel());
        $this->assertSame([], $links->all());
    }

    /**
     * @covers CL\LunaCore\Repo\Links::add
     */
    public function testAdd()
    {
        $links = new Links(new Model());
        $linkOne = $this->getLinkOne();
        $linkMany = $this->getLinkMany();

        $links
            ->add($linkOne)
            ->add($linkMany);

        $expected = [
            'one' => $linkOne,
            'many' => $linkMany,
        ];

        $this->assertSame($expected, $links->all());
    }

    /**
     * @covers CL\LunaCore\Repo\Links::getModels
     */
    public function testGetModels()
    {
        $model1 = new Model();
        $model2 = new Model();
        $model3 = new Model();

        $links = new Links(new Model());

        $linkOne = $this
            ->getLinkOne()
            ->set($model1);

        $linkMany = $this
            ->getLinkMany()
            ->add($model2)
            ->add($model3);

        $links
            ->add($linkOne)
            ->add($linkMany);

        $result = $links->getModels();

        $this->assertInstanceOf('SplObjectStorage', $result);
        $this->assertTrue($result->contains($model1));
        $this->assertTrue($result->contains($model2));
        $this->assertTrue($result->contains($model3));
    }

    /**
     * @covers CL\LunaCore\Repo\Links::isEmpty
     */
    public function testIsEmpty()
    {
        $links = new Links(new Model());

        $this->assertTrue($links->isEmpty());

        $links->add($this->getLinkOne());

        $this->assertFalse($links->isEmpty());
    }

    /**
     * @covers CL\LunaCore\Repo\Links::has
     */
    public function testHas()
    {
        $links = new Links(new Model());

        $this->assertFalse($links->has('one'));

        $links->add($this->getLinkOne());

        $this->assertTrue($links->has('one'));
    }

    /**
     * @covers CL\LunaCore\Repo\Links::get
     */
    public function testGet()
    {
        $links = new Links(new Model());

        $this->assertNull($links->get('one'));

        $link = $this->getLinkOne();
        $links->add($link);

        $this->assertEquals($link, $links->get('one'));
    }
}
