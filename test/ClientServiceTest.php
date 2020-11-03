<?php

namespace tests;

require_once 'vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use services\ClientService;

class ClientServiceTest extends TestCase
{
    public function testAuthTrue()
    {
        $myClient = new ClientService('http://testapi.ru');
        $this->assertTrue($myClient->auth('test','12345'));
    }

    public function testAuthFalse()
    {
        $myClient = new ClientService('http://testapi.ru');
        $this->assertFalse($myClient->auth('notest','12345'));
    }

    public function testAuthToken()
    {
        $myClient = new ClientService('http://testapi.ru');
        $myClient->auth('test','12345');
        $this->assertNotNull($myClient->token);
    }

    public function testGetUserTrue(){
        $myClient = new ClientService('http://testapi.ru');
        $myClient->auth('test','12345');
        $this->assertTrue($myClient->getUser('ivanov'));
    }

    public function testGetUserFalse(){
        $myClient = new ClientService('http://testapi.ru');
        $myClient->auth('test','12345');
        $this->assertFalse($myClient->getUser('noFIO'));
    }

    public function testGetUserIvanov(){
        $myClient = new ClientService('http://testapi.ru');
        $myClient->auth('test','12345');
        $myClient->getUser('ivanov');
        $template=<<<EOT
{
	"status": "OK",
	"active": "1",
	"blocked": false,
	"created_at": 1587457590,
	"id": 23,
	"name": "Ivanov Ivan",
	"permissions": [
    	{
        	"id": 1,
        	"permission": "comment"
    	},
    	{
        	"id": 2,
        	"permission": "upload photo"
    	},
    	{
        	"id": 3,
        	"permission": "add event"
    	}
	]
}
EOT;
        $array1 = (array) json_decode($template);
        $array2 = (array) $myClient->data;
        $result_array = array_diff($array1, $array2);
        $this->assertTrue(empty($result_array[0]));
    }

    public function testUpdateUser()
    {
        $myClient = new ClientService('http://testapi.ru');
        $myClient->auth('test', '12345');
        $data=<<<EOT
{
    "active": "1",
	"blocked": true,
	"name": "Petr Petrovich",
	"permissions": [
    	{
        	"id": 1,
        	"permission": "comment"
    	},
 	]
}
EOT;
        $this->assertTrue($myClient->updateUser(123, $data));
    }
}
