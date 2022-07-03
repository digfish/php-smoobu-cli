<?php
namespace digfish\smoobuclient\elements;


class SmoobuBooking
{
    var $arrivalDate;
    var $departureDate;
    var $channelId;
    var $apartmentId;
    var $arrivalTime;
    var $departureTime;
    var $firstName;
    var $lastName;
    var $email;
    var $phone;
    var $notice;
    var $adults;
    var $children;
    var $price;
    var $priceStatus;
    var $deposit;
    var $depositStatus;
    var $language;
    var $priceElements = [];

    public function __construct($fields = [])
    {
        if (is_object($fields)) {
            $fields = (array) $fields;
        }
        foreach ($fields as $key => $value) {
            $this->{$key} = $value;
        }
    }
}
