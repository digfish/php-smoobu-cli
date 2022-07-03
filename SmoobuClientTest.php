<?php
require "vendor/autoload.php";

use Dotenv\Dotenv;
use \PHPUnit\Framework\TestCase;
use digfish\smoobuclient\SmoobuClient;
use digfish\smoobuclient\elements\SmoobuBooking as Booking;

final class SmoobuClientTest extends TestCase
{
    var $client;
    static $last_apartment_id = 0;
    static $last_guest_id = 0;
    static $last_booking_id = 0;
    static $faker;
    static $apartments;

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

    public function testListApartments()
    {
        $apartments = $this->client->list_apartments();
        self::$apartments = $apartments;
        #var_dump($apartments);
        $this->assertTrue(count($apartments) > 0);
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
        $start = date('Y-m-d',strtotime('+1 year 1 day'));
        $end = date('Y-m-d',strtotime('+1 year 5 days'));
        $availability = $this->client->availability($start,$end,[]);
        sort($availability->availableApartments);
        $apartment_ids = array_map(function ($e) {
            return $e->id;
        }, self::$apartments);
        sort($apartment_ids);
         $this->assertEquals(
            $availability->availableApartments,
            $apartment_ids
         );
    }

    function testListBookings() {
        $apartment_id = self::$apartments[rand(0,count(self::$apartments)-1)]->id;
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
        #print("$arrival_date => $departure_date\n");
        $new_booking->arrivalDate = $arrival_date;
        $new_booking->departureDate = $departure_date;
        $new_booking->apartmentId = 1277051;
        $new_booking->channelId = 70;
        
        $new_booking->firstName = self::$faker->firstName();
        $new_booking->lastName = self::$faker->lastName();
        $new_booking->email = self::$faker->email();

        $created_booking = $this->client->create_booking($new_booking);
        #var_dump($created_booking);
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

    function testCancelAllBookings() {
        $apartments = self::$apartments;
        $counter = 0;
        foreach ($apartments as $apartment) {
            # code...
            $bookings = $this->client->list_bookings(['apartmentId' => $apartment->id]);
            foreach ($bookings as $booking) {
                # code...
                $this->client->cancel_booking($booking->id);
            }
            $bookings = $this->client->list_bookings(['apartmentId' => $apartment->id]);
            $this->assertTrue(count($bookings) == 0);
            $counter += count($bookings);
        }
        $this->assertTrue($counter == 0);
    }



    function testGetApartment() {
        $apartment = $this->client->get_apartment(1277051);
        $this->assertTrue($this->client->lastStatus == 200);
    }
}
