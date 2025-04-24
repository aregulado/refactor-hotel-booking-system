<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Room;
use App\Repositories\Interfaces\BookingRepositoryInterface;
use App\Services\Interfaces\AvailabilityServiceInterface;
use App\Services\Interfaces\BookingServiceInterface;
use App\Events\BookingCreated;

class BookingService implements BookingServiceInterface
{
    protected $bookingRepository;
    protected $availabilityService;

    /**
     * Constructor
     *
     * @param BookingRepositoryInterface $bookingRepository
     * @param AvailabilityServiceInterface $availabilityService
     */
    public function __construct(
        BookingRepositoryInterface $bookingRepository,
        AvailabilityServiceInterface $availabilityService
    ) {
        $this->bookingRepository = $bookingRepository;
        $this->availabilityService = $availabilityService;
    }

    /**
     * Create a new booking
     *
     * @param array $data
     * @param Room $room
     * @return Booking
     */
    public function createBooking(array $data, Room $room): Booking
    {
        // Prepare data for booking creation
        $bookingData = $data;
        $bookingData['room_id'] = $room->id;

        // Create booking
        $booking = $this->bookingRepository->createBooking($bookingData);

        // Invalidate cache
        $this->availabilityService->invalidateRoomAvailabilityCache($room->id);

        // Dispatch event for additional processing
        event(new BookingCreated($booking));

        return $booking;
    }
}