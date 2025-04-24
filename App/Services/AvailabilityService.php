<?php

namespace App\Services;

use App\Repositories\Interfaces\BookingRepositoryInterface;
use App\Services\Interfaces\AvailabilityServiceInterface;
use Illuminate\Support\Facades\Cache;

class AvailabilityService implements AvailabilityServiceInterface
{
    protected $bookingRepository;

    /**
     * Constructor
     *
     * @param BookingRepositoryInterface $bookingRepository
     */
    public function __construct(BookingRepositoryInterface $bookingRepository)
    {
        $this->bookingRepository = $bookingRepository;
    }

    /**
     * Check if room is available for the given date range
     *
     * @param int $roomId
     * @param string $checkInDate
     * @param string $checkOutDate
     * @return bool
     */
    public function isRoomAvailable(int $roomId, string $checkInDate, string $checkOutDate): bool
    {
        $cacheKey = "room_{$roomId}_availability_{$checkInDate}_{$checkOutDate}";

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($roomId, $checkInDate, $checkOutDate) {
            return !$this->bookingRepository->hasOverlappingBookings($roomId, $checkInDate, $checkOutDate);
        });
    }

    /**
     * Invalidate room availability cache
     *
     * @param int $roomId
     * @return void
     */
    public function invalidateRoomAvailabilityCache(int $roomId): void
    {
        Cache::forget("room_{$roomId}_availability_*");
    }
}