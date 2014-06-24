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
     * @covers ::onlySaved
     * @covers ::onlyDeleted
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
                'onlyDeleted',
                'onlySaved',
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
            ->with($this->identicalTo(3))
            ->will($this->returnValue($model));

        $repo
            ->expects($this->exactly(3))
            ->method('findAll')
            ->will($this->returnValue($find));

        $repo
            ->expects($this->once())
            ->method('findByName')
            ->with($this->identicalTo(5))
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

        $find
            ->expects($this->once())
            ->method('onlyDeleted')
            ->will($this->returnSelf());

        $find
            ->expects($this->once())
            ->method('onlySaved')
            ->will($this->returnSelf());

        $this->assertSame($model, ModelMock::find(3));
        $this->assertSame($model, ModelMock::findByName(5));
        $this->assertSame($find, ModelMock::findAll());
        $this->assertSame($repo, ModelMock::save($model));
        $this->assertSame($repo, ModelMock::saveArray($models));
        $this->assertSame($find, ModelMock::onlyDeleted());
        $this->assertSame($find, ModelMock::onlySaved());
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
     * @covers ::getLinkOrError
     */
    public function testGetLinkOrError()
    {
        $model = $this->getMock(
            __NAMESPACE__.'\Model',
            ['getLink'],
            [],
            '',
            false
        );
        $other = new Model();
        $otherVoid = new Model([], State::VOID);

        $repo = new Repo();

        $linkOne = new LinkOne($model, new RelOne('one', $repo, $repo), $other);
        $linkMany = new LinkMany($model, new RelMany('many', $repo, $repo), []);
        $linkOneVoid = new LinkOne($model, new RelOne('one', $repo, $repo), $otherVoid);

        $model
            ->expects($this->exactly(3))
            ->method('getLink')
            ->will($this->onConsecutiveCalls($linkOne, $linkMany, $linkOneVoid));

        $this->assertSame($linkOne, $model->getLinkOrError('test'));
        $this->assertSame($linkMany, $model->getLinkOrError('test'));

        $this->setExpectedException('LogicException', 'Link for rel test should not be void');

        $this->assertSame($linkOne, $model->getLinkOrError('test'));
    }

}
