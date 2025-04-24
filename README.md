# Scenario:
You’re reviewing a Laravel controller for a hotel booking engine used by a BGC hotel. The code handles booking creation but has issues impacting performance,
security, and maintainability.

Code Snippet:
```
// routes/api.php
use App\Http\Controllers\BookingController;
Route::post('/bookings', [BookingController::class, 'store']);

// app/Http/Controllers/BookingController.php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Room;
class BookingController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->all();
        $room = Room::where('number', $data['room_number'])->first();
        if ($room) {
            $booking = new Booking();
            $booking->guest_name = $data['guest_name'];
            $booking->room_number = $data['room_number'];
            $booking->check_in_date = $data['check_in_date'];
            $booking->check_out_date = $data['check_out_date'];
            $booking->status = 'pending';
            $booking->save();
            return response()->json(['message' => 'Booking created', 'booking' => $booking], 201);
        }
        return response()->json(['error' => 'Room not found'], 404);
    }
}
```

## Instructions:
1. Identify at least 5 issues in the code that could lead to bugs, security risks, or performance problems (e.g., N+1 queries, validation, security).
2. Provide a refactored version of the controller, addressing validation, security, and scalability (e.g., use form requests, transactions).
3. Write a brief review (max 1 page) explaining your findings, improvements, and why they matter for a high-traffic booking system. Include considerations for
   handling 10,000 bookings daily.
   Bonus:
   Suggest a caching strategy to improve performance.
   Propose a logging mechanism for failed bookings.