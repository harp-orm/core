<?php

namespace CL\LunaCore\Test;

use PHPUnit_Framework_TestCase;
use CL\EnvBackup\Env;
use CL\EnvBackup\DirectoryParam;

abstract class AbstractTestCase extends PHPUnit_Framework_TestCase
{
    private $env;

    public function getEnv()
    {
        return $this->env;
    }

    public function setUp()
    {
        parent::setUp();

        $this->env = new Env([
            new DirectoryParam(__DIR__.'/../repos', [
                'Address.json' => '{
                    "1": {
                        "id": 1,
                        "name": null,
                        "location": "test location"
                    }
                }',
                'Post.json' => '{
                    "1": {
                        "id": 1,
                        "name": "post 1",
                        "body": "my post 1",
                        "userId": 1
                    },
                    "2": {
                        "id": 2,
                        "name": "post 2",
                        "body": "my post 2",
                        "userId": 1
                    }
                }',
                'User.json' => '{
                    "1": {
                        "id": 1,
                        "name": "name",
                        "password": null,
                        "addressId": 1,
                        "isBlocked": true
                    }
                }',
            ])
        ]);

        $this->env->apply();

    }

    public function tearDown()
    {
        // $this->env->restore();
    }
}
