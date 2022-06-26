<?php
require "vendor/autoload.php";
include_once "smoobu_client.php";

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;


final class SmoobuClientTest extends TestCase
{
    var $client;
    static $last_apartment_id = 0;
    static $last_guest_id = 0;
    static $last_booking_id = 0;
    static $faker;

    protected function setUp(): void {
        //parent::setUp();
        $dotenv = Dotenv::createImmutable(__DIR__);
        $dotenv->load(); 

        $this->client = new SmoobuClient();
        self::$faker = Faker\Factory::create();
    }
    public function testSmoobuClient()
    {
        $this->assertInstanceOf(SmoobuClient::class, $this->client);
    }

    public function testCurrentUser() {
        $userinfo = $this->client->current_user();
        
        $this->assertTrue($userinfo->id == $_ENV['SMOOBU_TEST_USER_ID']);
    }

    public function testListGuests() {
        $guests = $this->client->list_guests();
        self::$last_guest_id = $guests[0]->id;
        $this->assertTrue(count($guests) > 0);
    }

    public function testGetGuest() {
        $guest = $this->client->get_guest(self::$last_guest_id);
        $this->assertTrue($guest->id == self::$last_guest_id);
    }

    function testAvailability() {
        $availability = $this->client->availability('2020-01-01','2020-01-02',[1277051]);
#        var_dump($availability);
        $this->assertEquals($availability->availableApartments[0], 1277051);
    }

    function testListBookings() {
        $query_args = [
             'created_from' => '2022-01-01',
             'created_to' => '2022-12-31',
             'from' => '2022-06-01',
             'to' => '2022-10-31',
             'modifiedFfrom' => '2022-01-01',
             'modifiedTo' => '2022-12-31',
             'arrivalFrom' => '2022-06-01',
             'arrivalTo' => '2022-09-30',
             'departureFrom' => '2022-06-01',
             'deparatureTo' => '2022-09-30',
             'showCancelled' => True,
             'excludeBocked' => False,
             'apartmentId' => 1277051,
             'includePriceElements' => True
        ];
        $bookings = $this->client->list_bookings($query_args);
        self::$last_booking_id = $bookings[0]->id;
        $this->assertTrue(count($bookings) > 0);
    }



    public function testCreateBooking() {
        $new_booking = new Booking();
        $arrival_time = time()+60*60*24*rand(120,150);
        $departure_time = $arrival_time + 60*60*24*2;
        $arrival_date = date('Y-m-d',$arrival_time);
        $departure_date = date('Y-m-d',$departure_time);
        print("$arrival_date => $departure_date");
        $new_booking->arrivalDate = $arrival_date;
        $new_booking->departureDate = $departure_date;
        $new_booking->apartmentId = 1277051;
        $new_booking->channelId = 70;
        $new_booking->firstName = 'Cristiano';
        $new_booking->lastName = 'Ronaldo';
        $new_booking->email = 'cr7@munited.co.uk';

        $created_booking = $this->client->create_booking($new_booking);
        var_dump($created_booking);
        $this->assertTrue(isset($created_booking->id));
        self::$last_booking_id = $created_booking->id;
    }

    public function testGetBooking() {
        $booking = $this->client->get_booking(self::$last_booking_id);
        $this->assertTrue($booking->id == self::$last_booking_id);
    }

    public function testUpdateBooking() {
        $booking_to_update = new Booking( $this->client->get_booking(self::$last_booking_id));
        $booking_to_update->prepayment = 30.0;
        $sucess = $this->client->update_booking(self::$last_booking_id,$booking_to_update);
        $this->assertEquals($sucess->status,200);
    }


    public function testCancelBooking()
    {
        $sucess = $this->client->cancel_booking(self::$last_booking_id);

        $this->assertTrue($sucess->success);
    }


    public function testListApartments() {
        $apartments = $this->client->list_apartments();
        
        $this->assertTrue(count($apartments) > 0);
    }

    function testGetApartment() {
        $apartment = $this->client->get_apartment(1277051);
        $this->assertTrue($this->client->lastStatus == 200);
    }
}
