<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\UnavailableSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingConfirmation;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::all();

        return response()->json($bookings);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'vehicle_make' => 'required',
            'vehicle_model' => 'required',
            'phone_number' => 'required',
            'booking_datetime' => 'required',
        ]);

        // Check if slot is unavailable.
        if (UnavailableSlot::where('unavailable_datetime', $request->booking_datetime)
        ->exists()) {
            return response()->json(['error' => 'This slot is unavailable'], 400);
        }

        try {
        
            $booking = new Booking;
            $booking->fill($request->all());
            $booking->save();

            // Create or update the corresponding UnavailableSlot record.
            $unavailableSlot = UnavailableSlot::updateOrCreate(
                ['unavailable_datetime' => $request->booking_datetime],
                ['booking_id' => $booking->id]
            );
        
            // Send confirmation emails to Paul and the customer.
            $email = new BookingConfirmation($booking);
            Mail::to($booking->email)->send($email);
            // Paul's email address.
            Mail::to('paul@example.com')->send($email);
        
            return response()->json($booking, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getBookingsByDate(Request $request, $date)
    {
        $bookings = Booking::whereDate('booking_datetime', $date)->get();

        return response()->json($bookings);
    }

    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->delete();

        // Delete the corresponding UnavailableSlot record.
        UnavailableSlot::where('booking_id', $id)->delete();



        return response()->json(['message' => 'Booking deleted successfully']);
    }

    public function update(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update($request->all());

        // Update the corresponding UnavailableSlot record.
        UnavailableSlot::updateOrCreate(
            ['unavailable_datetime' => $booking->booking_datetime],
            ['booking_id' => $booking->id]
        );

        return response()->json(['message' => 'Booking updated successfully', 'booking' => $booking]);
    }
    
}

