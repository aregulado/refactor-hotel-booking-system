<?php

namespace App\Services\Interfaces;

use App\Models\Booking;
use App\Models\Room;

interface BookingServiceInterface
{
    /**
     * Create a new booking
     *
     * @param array $data
     * @param Room $room
     * @return Booking
     */
    public function createBooking(array $data, Room $room): Booking;
}