<?php

namespace Test\Acceptance\Api;

use Guzzle\Http\Client;
use Symfony\Component\EventDispatcher\Event;

/**
 * Test class for Controller_Database.
 *
 * @author Jonathan Bernardi <spekkionu@spekkionu.com>
 * @license MIT http://opensource.org/licenses/MIT
 * @package Test
 * @subpackage Controller
 */
class DatabaseTest extends \Test_DatabaseTest
{

    /**
     * Guzzle client
     * @var Guzzle\Http\Client $client
     */
    protected $client;

    /**
     * Site config
     * @var array $config
     */
    protected $config;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        global $config;
        parent::setUp();
        // Register the connection
        \Model_Abstract::setConnection($this->dbh);
        $url = $config['test']['hostname'] . $config['test']['api_url'];
        $this->client = new Client($url);
        $this->client->getEventDispatcher()->addListener('request.before_send', function(Event $event) {
              $event['request']->addHeader('X-SERVER-MODE', 'test')->setAuth('admin', 'password');
          });
        $this->config = $config;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        \Model_Abstract::close();
        parent::tearDown();
    }

    /**
     * Controller_Database::listAction
     */
    public function testListAction()
    {
        $website_id = 1;
        $request = $this->client->get("api/database/{$website_id}");
        $response = $request->send();
        $this->assertEquals('application/json', $response->getContentType());
        $this->assertEquals(200, $response->getStatusCode());
        $body = json_decode($response->getBody(true), true);
        $this->assertCount(1, $body);
    }

    /**
     * Controller_Database::detailsAction
     */
    public function testDetailsAction()
    {
        $id = 1;
        $website_id = 1;
        $request = $this->client->get("api/database/{$website_id}/{$id}");
        $response = $request->send();
        $this->assertEquals('application/json', $response->getContentType());
        $this->assertEquals(200, $response->getStatusCode());
        $body = json_decode($response->getBody(true), true);
        $this->assertEquals($id, $body['id']);
    }

    /**
     * Controller_Database::addAction
     */
    public function testAddAction()
    {
        $website_id = 1;
        $result = array(
          'type' => 'mysql',
          'hostname' => 'localhost',
          'username' => 'pickle',
          'password' => 'password',
          'database' => 'dbname',
          'url' => 'http://url.com'
        );
        $request = $this->client->post("api/database/{$website_id}")->addPostFields($result);
        $response = $request->send();
        $this->assertEquals('application/json', $response->getContentType());
        $this->assertEquals(201, $response->getStatusCode());
        $body = json_decode($response->getBody(true), true);
        $this->assertEquals($result, array(
          'type' => $body['type'],
          'username' => $body['username'],
          'password' => $body['password'],
          'hostname' => $body['hostname'],
          'url' => $body['url'],
          'database' => $body['database']
        ));
    }

    /**
     * Controller_Database::updateAction
     */
    public function testUpdateAction()
    {
        $id = 1;
        $website_id = 1;
        $result = array(
          'type' => 'mysql',
          'hostname' => 'localhost',
          'username' => 'pickle',
          'password' => 'password',
          'database' => 'dbname',
          'url' => 'http://url.com'
        );
        $request = $this->client->post("api/database/{$website_id}/{$id}")->addPostFields($result);
        $request->addHeader('X-HTTP-Method-Override', 'PUT');
        $response = $request->send();
        $this->assertEquals('application/json', $response->getContentType());
        $this->assertEquals(200, $response->getStatusCode());
        $body = json_decode($response->getBody(true), true);
        $this->assertEquals($result, array(
          'type' => $body['type'],
          'username' => $body['username'],
          'password' => $body['password'],
          'hostname' => $body['hostname'],
          'url' => $body['url'],
          'database' => $body['database']
        ));
    }

    /**
     * Controller_Database::deleteAction
     */
    public function testDeleteAction()
    {
        $id = 1;
        $website_id = 1;
        $request = $this->client->post("api/database/{$website_id}/{$id}");
        $request->addHeader('X-HTTP-Method-Override', 'DELETE');
        $response = $request->send();
        $this->assertEquals(204, $response->getStatusCode());
    }

}


