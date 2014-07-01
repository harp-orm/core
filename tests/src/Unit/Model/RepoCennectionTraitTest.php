<?php

namespace Harp\Core\Test\Unit\Model;

use Harp\Core\Model\RepoConnectionTrait;
use Harp\Core\Test\AbstractTestCase;
use stdClass;
use Harp\Core\Model\State;
use Harp\Core\Repo\LinkOne;
use Harp\Core\Repo\LinkMany;

/**
 * @coversDefaultClass Harp\Core\Model\RepoConnectionTrait
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class RepoConnectionTraitTest extends AbstractTestCase
{
    /**
     * @covers ::find
     * @covers ::findByName
     * @covers ::findAll
     * @covers ::save
     * @covers ::saveArray
     */
    public function testFind()
    {
        $repo = $this->getMock(
            __NAMESPACE__.'\RepoMock',
            [
                'find',
                'findAll',
                'findByName',
                'save',
                'saveArray',
            ]
        );

        ModelMock::setRepoStatic($repo);

        $model = new ModelMock();
        $models = [$model];

        $find = $this->getMock(
            'Harp\Core\Test\Repo\Find',
            ['onlyDeleted', 'onlySaved'],
            [],
            '',
            false
        );

        $repo
            ->expects($this->once())
            ->method('find')
            ->with($this->identicalTo(3), $this->identicalTo(State::SAVED | State::DELETED))
            ->will($this->returnValue($model));

        $repo
            ->expects($this->once())
            ->method('findAll')
            ->will($this->returnValue($find));

        $repo
            ->expects($this->once())
            ->method('findByName')
            ->with($this->identicalTo('name'), $this->identicalTo(State::SAVED | State::DELETED))
            ->will($this->returnValue($model));

        $repo
            ->expects($this->once())
            ->method('save')
            ->with($this->identicalTo($model))
            ->will($this->returnSelf());

        $repo
            ->expects($this->once())
            ->method('saveArray')
            ->with($this->identicalTo($models))
            ->will($this->returnSelf());

        $this->assertSame($model, ModelMock::find(3, State::SAVED | State::DELETED));
        $this->assertSame($model, ModelMock::findByName('name', State::SAVED | State::DELETED));
        $this->assertSame($find, ModelMock::findAll());
        $this->assertSame($repo, ModelMock::save($model));
        $this->assertSame($repo, ModelMock::saveArray($models));
    }

    /**
     * @covers ::getId
     * @covers ::setId
     */
    public function testGetId()
    {
        $model = new Model();

        $this->assertEquals(null, $model->getId());

        $model->id = 20;

        $this->assertEquals(20, $model->getId());

        $model->setId(30);

        $this->assertEquals(30, $model->id);
        $this->assertEquals(30, $model->getId());
    }

    /**
      * @covers ::getRepo
      * @covers ::getRepoStatic
      */
    public function testGetRepo()
    {
        $model = new Model();

        $this->assertSame(Repo::get(), $model->getRepo());
        $this->assertSame(Repo::get(), Model::getRepoStatic());
    }

    /**
     * @covers ::getLink
     */
    public function testGetLink()
    {
        $repo = $this->getMock(__NAMESPACE__.'\Repo', ['loadLink']);

        $model = $this->getMock(
            __NAMESPACE__.'\Model',
            ['getRepo'],
            [],
            '',
            false
        );

        $link = new stdClass();

        $model
            ->expects($this->once())
            ->method('getRepo')
            ->will($this->returnValue($repo));

        $repo
            ->expects($this->once())
            ->method('loadLink')
            ->with($this->identicalTo($model), $this->equalTo('test'))
            ->will($this->returnValue($link));

        $result = $model->getLink('test');

        $this->assertSame($link, $result);
    }

    /**
     * @covers ::getLinkOne
     * @covers ::getLinkedModel
     * @covers ::setLinkedModel
     */
    public function testGetLinkOne()
    {
        $model2 = new Model();
        $model3 = new Model();
        $model = $this->getMock(__NAMESPACE__.'\Model', ['getLink'], [], 'MockLinkOne');

        $link1 = new LinkOne($model, new RelOne('test', Repo::get(), Repo::get()), $model2);
        $link2 = new LinkMany($model, new RelMany('test', Repo::get(), Repo::get()), []);

        $model
            ->expects($this->exactly(4))
            ->method('getLink')
            ->with($this->equalTo('test'))
            ->will($this->onConsecutiveCalls($link1, $link1, $link1, $link2));

        $result = $model->getLinkOne('test');

        $this->assertSame($link1, $result);

        $result = $model->getLinkedModel('test');

        $this->assertSame($model2, $result);

        $model->setLinkedModel('test', $model3);

        $this->assertSame($model3, $link1->get());

        $this->setExpectedException('LogicException', 'Rel test for MockLinkOne must be a valid RelOne');

        $model->getLinkOne('test');
    }

    /**
     * @covers ::getLinkMany
     */
    public function testGetLinkMany()
    {
        $model2 = new Model();
        $model = $this->getMock(__NAMESPACE__.'\Model', ['getLink'], [], 'MockLinkMany');

        $link1 = new LinkMany($model, new RelMany('test', Repo::get(), Repo::get()), []);
        $link2 = new LinkOne($model, new RelOne('test', Repo::get(), Repo::get()), $model2);

        $model
            ->expects($this->exactly(2))
            ->method('getLink')
            ->with($this->equalTo('test'))
            ->will($this->onConsecutiveCalls($link1, $link2));

        $result = $model->getLinkMany('test');

        $this->assertSame($link1, $result);

        $this->setExpectedException('LogicException', 'Rel test for MockLinkMany must be a valid RelMany');

        $model->getLinkMany('test');
    }
}
