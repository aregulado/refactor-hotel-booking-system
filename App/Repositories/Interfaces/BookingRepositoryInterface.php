<?php

namespace App\Repositories\Interfaces;

use App\Models\Room;
use App\Models\Booking;

interface BookingRepositoryInterface
{
    /**
     * Find a room by its number
     *
     * @param string $roomNumber
     * @return Room|null
     */
    public function findRoomByNumber(string $roomNumber): ?Room;

    /**
     * Create a new booking
     *
     * @param array $data
     * @return Booking
     */
    public function createBooking(array $data): Booking;

    /**
     * Check if there are any overlapping bookings for a room
     *
     * @param int $roomId
     * @param string $checkInDate
     * @param string $checkOutDate
     * @return bool
     */
    public function hasOverlappingBookings(int $roomId, string $checkInDate, string $checkOutDate): bool;
}
