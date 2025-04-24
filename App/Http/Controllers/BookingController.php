<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Repositories\Interfaces\BookingRepositoryInterface;
use App\Services\Interfaces\AvailabilityServiceInterface;
use App\Services\Interfaces\BookingServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    protected $bookingService;
    protected $availabilityService;
    protected $bookingRepository;

    /**
     * Constructor with dependency injection
     *
     * @param BookingServiceInterface $bookingService
     * @param AvailabilityServiceInterface $availabilityService
     * @param BookingRepositoryInterface $bookingRepository
     */
    public function __construct(
        BookingServiceInterface $bookingService,
        AvailabilityServiceInterface $availabilityService,
        BookingRepositoryInterface $bookingRepository
    ) {
        $this->bookingService = $bookingService;
        $this->availabilityService = $availabilityService;
        $this->bookingRepository = $bookingRepository;
    }

    /**
     * Create a new booking.
     *
     * @param StoreBookingRequest $request
     * @return JsonResponse
     */
    public function store(StoreBookingRequest $request): JsonResponse
    {
        try {
            // Get validated data
            $validatedData = $request->validated();

            // Check if room exists
            $room = $this->bookingRepository->findRoomByNumber($validatedData['room_number']);

            if (!$room) {
                $this->logFailedBooking('Room not found', $validatedData);
                return response()->json(['error' => 'Room not found'], 404);
            }

            // Check room availability
            if (!$this->availabilityService->isRoomAvailable(
                $room->id,
                $validatedData['check_in_date'],
                $validatedData['check_out_date']
            )) {
                $this->logFailedBooking('Room not available for requested dates', $validatedData);
                return response()->json([
                    'error' => 'Room is not available for the requested dates'
                ], 422);
            }

            // Create booking
            $booking = $this->bookingService->createBooking($validatedData, $room);

            return response()->json([
                'message' => 'Booking created successfully',
                'booking' => $booking
            ], 201);

        } catch (\Exception $e) {
            Log::error('Booking creation failed: ' . $e->getMessage(), [
                'user_id' => $request->user() ? $request->user()->id : null,
                'data' => $request->except(['password', 'credit_card']),
                'exception' => get_class($e)
            ]);

            return response()->json(['error' => 'Failed to create booking'], 500);
        }
    }

    /**
     * Log failed booking attempts
     *
     * @param string $reason
     * @param array $data
     * @return void
     */
    private function logFailedBooking(string $reason, array $data): void
    {
        Log::channel('bookings')->warning('Failed booking attempt: ' . $reason, [
            'data' => array_diff_key($data, array_flip(['credit_card', 'password'])),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toDateTimeString()
        ]);
    }
}