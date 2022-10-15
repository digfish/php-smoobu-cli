### SMOOBU API CLIENT IN PHP ###

This is a implementation in PHP of the [Smoobu](https://smoobu.com) API, an property management system. Their API allow the creation of bookings, updating it, list and create the guests that made the bookings, send messages to the clients.  Their complete api is [described here](https://docs.smoobu.com/).

## Install

The package is hosted on [packagist](http://packagist.org). To install run:
```
composer install digfish/php-smoobu-cli
```


## Environment variables
The variable `SMOOBU_TEST_API_KEY` should hold the value of your API key. You can set this via a .`env` file or your own code using `putenv` or `$_ENV['SMOOBU_TEST_API_KEY']`. If the application does not find the `.env` file it assumes that you are running on production. When in production, fill the values in `env.php`.


## What is implemented

|  Method          | API                                    |
|------------------|----------------------------------------|
|List Guests       | GET /guests                            |
|Get Guest         | GET /guests/:id                        |
|Avaiability       | GET /booking/checkApartmentAvailability|
|List Bookings     | GET /reservations                      |
|Create Booking    | POST /reservations                     |
|Update Booking    | PUT /reservations/:id                  |
|Cancel Booking    | DELETE /reservations/:id               |
|List Apartments   | GET /apartments                        |
|Get Apartment     | GET /apartments/:id                    |
-------------------------------------------------------------
