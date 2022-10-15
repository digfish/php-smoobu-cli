<?php 

namespace digfish\smoobuclient;

require_once "vendor/autoload.php";

use GuzzleHttp\Client;
use Dotenv\Dotenv;
use GuzzleHttp\Exception\ClientException;

use digfish\smoobuclient\elements\SmoobuBooking;



class SmoobuClient {

    

    private $client;

       
    function __construct(){
        $this->client = new Client([
            'base_uri' => 'https://login.smoobu.com/api/',
            'timeout'  => 60.0,
        ]);
        if (file_exists('.env')) {
            $dotenv = Dotenv::createImmutable('.');
            $dotenv->load();
            $_ENV['SMOOBU_API_KEY'] = $_ENV['SMOOBU_TEST_API_KEY'];
            $_ENV['SMOOBU_USER_ID'] = $_ENV['SMOOBU_TEST_USER_ID'];
        } else {
            require_once "env.php";
        }
    }

    protected function _invoke($uri,$http_met='GET',$data=[],$params=[],$headers=[]) {
        $resp = null;
        $headers['Api-Key'] = $_ENV['SMOOBU_API_KEY'];
        try {
            $resp = $this->client->request($http_met,$uri,[
                'headers' => $headers,
                'body' => json_encode($data),
                'query' => $params,
            ]);
        } catch (ClientException $e) {
            echo ($e->getMessage());
            $resp = $e->getResponse();
        }
        $this->lastStatus = $resp->getStatusCode(); 
        return json_decode($resp->getBody()->getContents());
    } 


    function current_user() {
        return $this->_invoke('me','GET');
    }

    function list_guests() {
        $response = $this->_invoke('guests','GET');
        if ($response->totalItems > 0) {
            return $response->guests;
        } else {
            return [];
        }
    }

    function get_guest($id) {
        return $this->_invoke('guests/'.$id,'GET');
    }

    function availability($arrival_date,$depature_date,$apartments) {
        return $this->_invoke('/booking/checkApartmentAvailability','POST',[
            'customerId' => $_ENV['SMOOBU_USER_ID'],
            'arrivalDate' => $arrival_date,
            'departureDate'=> $depature_date,
            "apartments" => $apartments
        ]);
    }

    function list_bookings($query_args=[]) {
        $bookings = $this->_invoke('reservations','GET',[],$query_args);
        return $bookings->bookings;
    }

    function get_booking($id) {
        return $this->_invoke('reservations/'.$id,'GET');
    }

    function create_booking(SmoobuBooking $new_booking) {
        $response = $this->_invoke('reservations','POST',$new_booking);
        if (!isset($response->id)) {
            return false;
        } else {
            return $response;
        }
    }

    function update_booking($booking_id,SmoobuBooking $booking_to_update) {
        return $this->_invoke('reservations/'.$booking_id,'PUT',$booking_to_update);
    }

    function cancel_booking($booking_id) {
        return $this->_invoke('reservations/'.$booking_id,'DELETE');
    }

    function list_apartments() {
        $apartments = $this->_invoke('apartments','GET');
        return $apartments->apartments;
    }

    function get_apartment($apartment_id) {
        return $this->_invoke('apartments/'.$apartment_id,'GET');
    }
}


