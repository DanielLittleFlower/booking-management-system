<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\UnavailableSlot;
use Illuminate\Http\Request;
use Carbon\Carbon;


class UnavailableSlotController extends Controller
{
    public function index()
    {
        $unavailableSlots = UnavailableSlot::all();

        return response()->json($unavailableSlots);
    }
    
    public function store(Request $request)
    {
        $unavailableSlot = new UnavailableSlot;
        $unavailableSlot->fill($request->all());
        $unavailableSlot->save();

        return response()->json($unavailableSlot, 201);
    }

    public function getAvailableSlots(Request $request)
    {
        // Define the working hours.
        $workingHoursStart = 9;
        $workingHoursEnd = 17.5;

        // Get today's date.
        $date = Carbon::today();

        // Create a collection to hold all of the half-hour slots in a day.
        $slots = collect();

        for ($hour = $workingHoursStart; $hour < $workingHoursEnd; $hour += 0.5) {
            $slots->push(Carbon::createFromTime($hour, $hour % 1 == 0.5 ? 30 : 0));
        }

        // Fetch unavailable slots and booked slots from the database.
        $unavailableSlots = UnavailableSlot::whereDate('unavailable_datetime', $date)->get();
        $bookedSlots = Booking::whereDate('booking_datetime', $date)->get();

        // Filter out the unavailable and booked slots.
        $availableSlots = $slots->filter(function ($slot) use ($unavailableSlots, $bookedSlots) {
            foreach ($unavailableSlots as $unavailableSlot) {
                if ($slot->equalTo($unavailableSlot->unavailable_datetime)) {
                    return false;
                }
            }

            foreach ($bookedSlots as $bookedSlot) {
                if ($slot->equalTo($bookedSlot->booking_datetime)) {
                    return false;
                }
            }

            return true;
        });

        return response()->json($availableSlots);
    }
}

