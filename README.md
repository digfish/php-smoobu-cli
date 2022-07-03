### SMOOBU API CLIENT IN PHP ###

This is a implementation of the [Smoobu](https://smoobu.com), an property management system. Their API allow the creation of bookings, updating it, list and create the guests that made the bookings, send messages to the clients. Allows synchronization of prices between different HMS systems, etc. Their complete api is [described here](https://docs.smoobu.com/).

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
|GET Apartments    | GET /apartments/:id                    |
-------------------------------------------------------------