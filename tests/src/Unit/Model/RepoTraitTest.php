<?php

namespace Harp\Core\Test\Unit\Model;

use Harp\Core\Model\RepoTrait;
use Harp\Core\Model\AbstractModel;
use Harp\Core\Test\Integration\AbstractIntegrationTestCase;
use Harp\Core\Test\Model\User;
use Harp\Core\Test\Model\Post;
use Harp\Core\Test\Model\Address;
use Harp\Core\Model\State;
use Harp\Core\Repo\LinkOne;
use Harp\Core\Repo\LinkMany;
use Harp\Core\Test\Repo\TestRepo;
use stdClass;

/**
 * @coversDefaultClass Harp\Core\Model\RepoTrait
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class RepoTraitTest extends AbstractIntegrationTestCase
{
    /**
     * @covers ::getRepo
     */
    public function testGetRepo()
    {
        $repo1 = Model::getRepo();
        $repo2 = SoftDeleteModel::getRepo();

        $this->assertInstanceOf('Harp\Core\Test\Repo\TestRepo', $repo1);
        $this->assertInstanceOf('Harp\Core\Test\Repo\TestRepo', $repo2);
        $this->assertNotSame($repo1, $repo2);

        $this->assertEquals(__NAMESPACE__.'\Model', $repo1->getModelClass());
        $this->assertEquals(__NAMESPACE__.'\SoftDeleteModel', $repo2->getModelClass());
    }

    /**
     * @covers ::initialize
     */
    public function testInitialize()
    {
        $repo = new TestRepo(__NAMESPACE__.'\Model');

        AbstractModel::initialize($repo);
    }

    /**
     * @covers ::getPrimaryKey
     */
    public function testGetPrimaryKey()
    {
        $this->assertEquals('id', Model::getPrimaryKey());
    }

    /**
     * @covers ::getNameKey
     */
    public function testGetNameKey()
    {
        $this->assertEquals('name', Model::getNameKey());
    }

    /**
     * @covers ::find
     */
    public function testFind()
    {
        $user = User::find(2, State::DELETED);

        $this->assertEquals('deleted', $user->name);
    }

    /**
     * @covers ::findByName
     */
    public function testFindByName()
    {
        $user = User::findByName('deleted', State::DELETED);

        $this->assertEquals('deleted', $user->name);
    }

    /**
     * @covers ::save
     */
    public function testSave()
    {
        $user = User::find(1);

        $user->name = 'new name';

        User::save($user);

        User::getRepo()->clear();

        $user = User::find(1);

        $this->assertEquals('new name', $user->name);
    }

    /**
     * @covers ::saveArray
     */
    public function testSaveArray()
    {
        $posts = Post::findAll()->load()->toArray();

        $posts[0]->name = 'new post name 1';
        $posts[1]->name = 'new post name 2';

        Post::saveArray($posts);

        Post::getRepo()->clear();

        $posts = Post::findAll()->load();

        $this->assertEquals('new post name 1', $posts->getFirst()->name);
        $this->assertEquals('new post name 2', $posts->getNext()->name);
    }

    /**
     * @covers ::getId
     * @covers ::setId
     */
    public function testGetSetId()
    {
        $post = Post::find(2);

        $this->assertEquals(2, $post->getId());

        $post->setId(4);

        $this->assertEquals(4, $post->getId());
    }

    /**
     * @covers ::getLink
     */
    public function testGetLink()
    {
        $user = User::find(1);

        $link = $user->getLink('address');

        $this->assertSame($user, $link->getModel());
        $this->assertSame(Address::getRepo(), $link->getRel()->getForeignRepo());
    }

    /**
     * @covers ::getLinkOne
     * @covers ::get
     * @covers ::set
     */
    public function testGetLinkOne()
    {
        $model2 = new Model();
        $model3 = new Model();
        $model = $this->getMock(__NAMESPACE__.'\Model', ['getLink'], [], 'MockLinkOne');

        $link1 = new LinkOne($model, new RelOne('test', Model::getRepo(), Model::getRepo()), $model2);
        $link2 = new LinkMany($model, new RelMany('test', Model::getRepo(), Model::getRepo()), []);

        $model
            ->expects($this->exactly(4))
            ->method('getLink')
            ->with($this->equalTo('test'))
            ->will($this->onConsecutiveCalls($link1, $link1, $link1, $link2));

        $result = $model->getLinkOne('test');

        $this->assertSame($link1, $result);

        $result = $model->get('test');

        $this->assertSame($model2, $result);

        $model->set('test', $model3);

        $this->assertSame($model3, $link1->get());

        $this->setExpectedException('LogicException', 'Rel test for MockLinkOne must be a valid RelOne');

        $model->getLinkOne('test');
    }

    /**
     * @covers ::all
     */
    public function testGetLinkMany()
    {
        $model2 = new Model();
        $model = $this->getMock(__NAMESPACE__.'\Model', ['getLink'], [], 'MockLinkMany');

        $link1 = new LinkMany($model, new RelMany('test', Model::getRepo(), Model::getRepo()), []);
        $link2 = new LinkOne($model, new RelOne('test', Model::getRepo(), Model::getRepo()), $model2);

        $model
            ->expects($this->exactly(2))
            ->method('getLink')
            ->with($this->equalTo('test'))
            ->will($this->onConsecutiveCalls($link1, $link2));

        $result = $model->all('test');

        $this->assertSame($link1, $result);

        $this->setExpectedException('LogicException', 'Rel test for MockLinkMany must be a valid RelMany');

        $model->all('test');
    }
}
