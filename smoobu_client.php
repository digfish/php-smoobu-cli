<?php 

require_once "vendor/autoload.php";

use GuzzleHttp\Client;
use Dotenv\Dotenv;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;

class SmoobuClient {

    private $client;

    function __construct(){
        $this->client = new Client([
            'base_uri' => 'https://login.smoobu.com/api/',
            'timeout'  => 60.0,
        ]);
        $dotenv = Dotenv::createImmutable(__DIR__);
        $dotenv->load(); 
    }

    protected function _invoke($uri,$http_met='GET',$data=[]) {
        $resp = null;
        try {
            $resp = $this->client->request($http_met,$uri,[
                'headers' => [
                    'Api-Key'=> $_ENV['SMOOBU_TEST_API_KEY'],
                    
                ],
                'body' => json_encode($data)
            ]);
        } catch (ClientException $e) {
            echo ($e->getMessage());
            return $resp;
        } 
        
        return json_decode($resp->getBody()->getContents());
    } 


    function current_user() {
        return $this->_invoke('me','GET');
    }

    function get_guests() {
        $response = $this->_invoke('guests','GET');
        if ($response->totalItems > 0) {
            return $response->guests;
        } else {
            return [];
        }
    }

    function availability($arrival_date,$depature_date,$apartments) {
        return $this->_invoke('/booking/checkApartmentAvailability','POST',[
            'customerId' => $_ENV['SMOOBU_TEST_USER_ID'],
            'arrivalDate' => $arrival_date,
            'departureDate'=> $depature_date,
            "apartments" => $apartments
        ]);
    }
}

// $smoobu = new SmoobuClient();
// var_dump($smoobu->current_user());
// var_dump($smoobu->get_guests());
// var_dump($smoobu->availability('2022-01-08','2022-01-10',[1277051]));

