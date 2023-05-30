<?php


namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingConfirmation;
use Tests\TestCase;
use App\Models\Booking;
use App\Models\UnavailableSlot;



class BookingTest extends TestCase
{
    use RefreshDatabase;

  public function test_booking_can_be_created()
  {
       
      // Prepare data for the booking
      $bookingData = [
        'name' => 'Test Name',
        'email' => 'test@test.com',
        'vehicle_make' => 'Test Vehicle Make',
        'vehicle_model' => 'Test Vehicle Model',
        'phone_number' => '445556543',
        'booking_datetime' => '2023-10-11 09:00:00', 
      ];
    
      // Fake the Mail 
      Mail::fake();

      // Send a POST request to the API
      $response = $this->postJson('/api/booking', $bookingData);

      // Check that the booking was created successfully
      $response->assertStatus(201);
      $this->assertCount(1, Booking::all());

      // Assert a mailable was sent
      Mail::assertSent(BookingConfirmation::class);
  }


  public function test_slot_can_be_marked_unavailable()
  {
      // Prepare data for the unavailable slot
      $slotData = [
          'unavailable_datetime' => '2023-10-11 09:00:00',
      ];

      // Send a POST request to the API
      $response = $this->postJson('/api/unavailable-slots', $slotData);

      // Check that the slot was marked as unavailable successfully
      $response->assertStatus(201);
      $this->assertCount(1, UnavailableSlot::all());
  }

  public function test_booking_cannot_be_made_for_unavailable_slot()
  {
      // Prepare data for the unavailable slot
      $slotData = [
          'unavailable_datetime' => '2023-10-11 10:00:00',
      ];

      // Mark the slot as unavailable
      UnavailableSlot::create($slotData);

      // Prepare data for the booking
      $bookingData = [
          'name' => 'Test Name',
          'email' => 'test@test.com',
          'vehicle_make' => 'Test Vehicle Make',
          'vehicle_model' => 'Test Vehicle Model',
          'phone_number' => '44123456789',
          'booking_datetime' => '2023-10-11 10:00:00',
      ];

      // Send a POST request to the API
      $response = $this->postJson('/api/booking', $bookingData);

      // Check that the booking was not created
      $response->assertStatus(400);
      $this->assertCount(0, Booking::all());
  }



}


