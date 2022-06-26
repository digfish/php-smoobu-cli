<?php
require "vendor/autoload.php";
include_once "smoobu_client.php";

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;

final class SmoobuClientTest extends TestCase
{
    var $client;

    protected function setUp(): void {
        //parent::setUp();
        $dotenv = Dotenv::createImmutable(__DIR__);
        $dotenv->load(); 

        $this->client = new SmoobuClient();
    }
    public function testSmoobuClient()
    {
        $this->assertInstanceOf(SmoobuClient::class, $this->client);
    }

    public function testCurrentUser() {
        $userinfo = $this->client->current_user();
        
        $this->assertTrue($userinfo->id == $_ENV['SMOOBU_TEST_USER_ID']);
    }

    public function testGetGuests() {
        $guests = $this->client->get_guests();
        $this->assertTrue(count($guests) > 0);
    }

    function testAvailability() {
        $availability = $this->client->availability('2020-01-01','2020-01-02',[1277051]);
#        var_dump($availability);
        $this->assertEquals($availability->availableApartments[0], 1277051);
    }
}
