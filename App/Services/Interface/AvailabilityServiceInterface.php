<?php

namespace App\Services\Interfaces;

interface AvailabilityServiceInterface
{
    /**
     * Check if room is available for the given date range
     *
     * @param int $roomId
     * @param string $checkInDate
     * @param string $checkOutDate
     * @return bool
     */
    public function isRoomAvailable(int $roomId, string $checkInDate, string $checkOutDate): bool;

    /**
     * Invalidate room availability cache
     *
     * @param int $roomId
     * @return void
     */
    public function invalidateRoomAvailabilityCache(int $roomId): void;
}