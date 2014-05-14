<?php

namespace CL\LunaCore\Test\Unit\Repo;

use CL\LunaCore\Repo\Rels;

class RelsTest extends AbstractRepoTestCase
{
    /**
     * @covers CL\LunaCore\Repo\Rels::all
     */
    public function testConstruct()
    {
        $rels = new Rels();

        $this->assertSame([], $rels->all());
    }

    /**
     * @covers CL\LunaCore\Repo\Rels::add
     * @covers CL\LunaCore\Repo\Rels::set
     */
    public function testAddAndSet()
    {
        $rels = new Rels();
        $relOne = $this->getRelOne();
        $relMany = $this->getRelMany();

        $rels
            ->add($relOne)
            ->set([
                $relMany,
            ]);

        $expected = [
            'many' => $relMany,
            'one' => $relOne,
        ];

        $this->assertEquals($expected, $rels->all());
    }

    /**
     * @covers CL\LunaCore\Repo\Rels::isEmpty
     */
    public function testIsEmpty()
    {
        $rels = new Rels();

        $this->assertTrue($rels->isEmpty());

        $rels->add($this->getRelOne());

        $this->assertFalse($rels->isEmpty());
    }

    /**
     * @covers CL\LunaCore\Repo\Rels::has
     */
    public function testHas()
    {
        $rels = new Rels();

        $this->assertFalse($rels->has('one'));

        $rels->add($this->getRelOne());

        $this->assertTrue($rels->has('one'));
    }

    /**
     * @covers CL\LunaCore\Repo\Rels::get
     */
    public function testGet()
    {
        $rels = new Rels();

        $this->assertNull($rels->get('one'));

        $link = $this->getRelOne();
        $rels->add($link);

        $this->assertEquals($link, $rels->get('one'));
    }
}
