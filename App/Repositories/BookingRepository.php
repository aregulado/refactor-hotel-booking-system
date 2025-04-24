<?php

namespace App\Repositories;

use App\Models\Room;
use App\Models\Booking;
use App\Repositories\Interfaces\BookingRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class BookingRepository implements BookingRepositoryInterface
{
    /**
     * Find a room by its number
     *
     * @param string $roomNumber
     * @return Room|null
     */
    public function findRoomByNumber(string $roomNumber): ?Room
    {
        $cacheKey = 'room_' . $roomNumber;
        return Cache::remember($cacheKey, now()->addHours(1), function () use ($roomNumber) {
            return Room::where('number', $roomNumber)->first();
        });
    }

    /**
     * Create a new booking
     *
     * @param array $data
     * @return Booking
     */
    public function createBooking(array $data): Booking
    {
        return DB::transaction(function () use ($data) {
            $booking = new Booking();
            $booking->guest_name = $data['guest_name'];
            $booking->room_id = $data['room_id'];
            $booking->room_number = $data['room_number'];
            $booking->check_in_date = $data['check_in_date'];
            $booking->check_out_date = $data['check_out_date'];
            $booking->guest_email = $data['guest_email'] ?? null;
            $booking->guest_phone = $data['guest_phone'] ?? null;
            $booking->status = 'pending';
            $booking->save();

            return $booking;
        });
    }

    /**
     * Check if there are any overlapping bookings for a room
     *
     * @param int $roomId
     * @param string $checkInDate
     * @param string $checkOutDate
     * @return bool
     */
    public function hasOverlappingBookings(int $roomId, string $checkInDate, string $checkOutDate): bool
    {
        return Booking::where('room_id', $roomId)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($checkInDate, $checkOutDate) {
                $query->whereBetween('check_in_date', [$checkInDate, $checkOutDate])
                    ->orWhereBetween('check_out_date', [$checkInDate, $checkOutDate])
                    ->orWhere(function ($q) use ($checkInDate, $checkOutDate) {
                        $q->where('check_in_date', '<=', $checkInDate)
                            ->where('check_out_date', '>=', $checkOutDate);
                    });
            })
            ->exists();
    }
}