<?php

namespace Harp\Core\Test\Unit\Repo;

use Harp\Core\Repo\Links;

/**
 * @coversDefaultClass Harp\Core\Repo\Links
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class LinksTest extends AbstractRepoTestCase
{
    /**
     * @covers ::getModel
     * @covers ::__construct
     * @covers ::all
     */
    public function testConstruct()
    {
        $model = new Model();
        $links = new Links($model);

        $this->assertSame($model, $links->getModel());
        $this->assertSame([], $links->all());
    }

    /**
     * @covers ::add
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
     * @covers ::getModels
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

        $this->assertInstanceOf('Harp\Core\Model\Models', $result);
        $this->assertTrue($result->has($model1));
        $this->assertTrue($result->has($model2));
        $this->assertTrue($result->has($model3));
    }

    /**
     * @covers ::isEmpty
     */
    public function testIsEmpty()
    {
        $links = new Links(new Model());

        $this->assertTrue($links->isEmpty());

        $links->add($this->getLinkOne());

        $this->assertFalse($links->isEmpty());
    }

    /**
     * @covers ::has
     */
    public function testHas()
    {
        $links = new Links(new Model());

        $this->assertFalse($links->has('one'));

        $links->add($this->getLinkOne());

        $this->assertTrue($links->has('one'));
    }

    /**
     * @covers ::get
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
