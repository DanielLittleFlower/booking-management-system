<template>
  <div class="p-4">
    <h2 class="text-xl font-bold mb-4">Upcoming Bookings</h2>
    <div class="flex items-center mb-4">
      <label for="filterDate" class="mr-2">Filter by Date:</label>
      <input type="date" id="filterDate" v-model="selectedDate" @change="filterBookingsByDate" class="border border-gray-300 rounded px-2 py-1" />
    </div>
    <table class="table-auto w-full">
      <thead>
        <tr>
          <th class="px-4 py-2">Name</th>
          <th class="px-4 py-2">Email</th>
          <th class="px-4 py-2">Phone</th>
          <th class="px-4 py-2">Vehicle Make</th>
          <th class="px-4 py-2">Vehicle Model</th>
          <th class="px-4 py-2">Date & Time</th>
          <th class="px-4 py-2">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="booking in filteredBookings" :key="booking.id">
          <td class="border px-4 py-2">{{ booking.name }}</td>
          <td class="border px-4 py-2">{{ booking.email }}</td>
          <td class="border px-4 py-2">{{ booking.phone_number }}</td>
          <td class="border px-4 py-2">{{ booking.vehicle_make }}</td>
          <td class="border px-4 py-2">{{ booking.vehicle_model }}</td>
          <td class="border px-4 py-2">{{ formatDate(booking.booking_datetime) }}</td>
          <td class="border px-4 py-2">
            <button @click="deleteBooking(booking.id)" class="bg-red-500 text-white px-4 py-2 rounded">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>
    <div class="mt-4">
      <h2 class="text-xl font-bold mb-2">Prevent Specific Days and Slots:</h2>
      <div class="flex items-center mb-2">
        <label for="preventDate" class="mr-2">Date:</label>
        <input type="date" id="preventDate" v-model="preventDate" class="border border-gray-300 rounded px-2 py-1" />
      </div>
      <div class="flex items-center">
        <label for="preventSlot" class="mr-2">Slot:</label>
        <select id="preventSlot" v-model="preventSlot" class="border border-gray-300 rounded px-2 py-1">
          <option value="">Select Slot</option>
          <option v-for="slot in slots" :key="slot" :value="slot">{{ slot }}</option>
        </select>
      </div>
      <button @click="addUnavailableSlot" class="mt-2 bg-red-500 text-white px-4 py-2 rounded">Add Unavailable Slot</button>
    </div>

    <div class="mt-4">
    <h2 class="text-xl font-bold mb-2">Unavailable Slots:</h2>
    <div class="flex items-center mb-2">
      <label for="filterUnavailableDate" class="mr-2">Filter by Date:</label>
      <input type="date" id="filterUnavailableDate" v-model="selectedUnavailableDate" @change="filterUnavailableSlotsByDate" class="border border-gray-300 rounded px-2 py-1" />
    </div>
    <ul>
      <li v-for="slot in filteredUnavailableSlots" :key="slot.id" class="border-b py-4">
        <p class="mb-2"><span class="font-semibold">Date & Time:</span> {{ slot.unavailable_datetime }}</p>
      </li>
    </ul>
  </div>  
</div>
</template>


<script>
import axios from 'axios';

export default {
  data() {
    return {
      bookings: [], // list of bookings
      selectedDate: null, // selected date for filtering bookings
      unavailableSlots: [], // list of unavailable slots
      preventDate: null, // selected date to prevent bookings
      preventSlot: null, // selected slot to prevent bookings
      slots: this.createSlots(), // available slots
      selectedUnavailableDate: null, // selected date for filtering unavailable slots
    };
  },
  mounted() {
    this.fetchBookings(); // Fetch the bookings and unavailable slots when the component is mounted
    this.fetchUnavailableSlots();
  },
  computed: {
    filteredBookings() {
      // Filter the bookings based on the selected date
      if (this.selectedDate) {
        const selectedDate = new Date(this.selectedDate);
        const selectedDateString = selectedDate.toISOString().split('T')[0];
        return this.bookings.filter(booking => {
          const bookingDate = new Date(booking.booking_datetime);
          const bookingDateString = bookingDate.toISOString().split('T')[0];
          return bookingDateString === selectedDateString;
        });
      } else {
        return this.bookings;
      }
    },
    filteredUnavailableSlots() {
      // Filter the unavailable slots based on the selected date and booking_id = null
      if (this.selectedUnavailableDate) {
        const selectedDate = new Date(this.selectedUnavailableDate);
        const selectedDateString = selectedDate.toISOString().split('T')[0];
        return this.unavailableSlots.filter(slot => {
          const slotDate = new Date(slot.unavailable_datetime);
          const slotDateString = slotDate.toISOString().split('T')[0];
          return slotDateString === selectedDateString && slot.booking_id === null;
        });
      } else {
        return this.unavailableSlots.filter(slot => slot.booking_id === null);
      }
    },
  },
  methods: {
    fetchBookings() {
      // Fetch the bookings data (server)
      axios.get('/api/bookings')
        .then(response => {
          this.bookings = response.data;
        })
        .catch(error => {
          console.error('Error fetching bookings:', error);
        });
    },
    fetchUnavailableSlots() {
      // Fetch the unavailable slots data (server)
      axios.get('/api/unavailable-slots')
        .then(response => {
          this.unavailableSlots = response.data;
        })
        .catch(error => {
          console.error('Error fetching unavailable slots:', error);
        });
    },
    deleteBooking(id) {
      // Send a request to delete the booking with the given ID
      axios.delete(`/api/bookings/${id}`)
        .then(response => {
          alert('Booking deleted successfully!');
          this.fetchBookings(); // Refresh the list of bookings
        })
        .catch(error => {
          console.error('Error deleting booking:', error);
        });
    },
    filterBookingsByDate() {
      // Refresh the list of bookings based on the selected date
      this.fetchBookings();
    },
    filterUnavailableSlotsByDate() {
      // Refresh the list of unavailable slots based on the selected date
      this.fetchUnavailableSlots();
    },
    isSlotUnavailable(bookingDateTime) {
      // Check if the given booking date and time is marked as unavailable
      return this.unavailableSlots.some(slot => slot.booking_datetime === bookingDateTime);
    },
    createSlots() {
      // Create a list of available slots
      const slots = [];
      for (let i = 9; i < 18; i++) {
        const start = i.toString().padStart(2, '0'); 
        const end = (i + 1).toString().padStart(2, '0'); 
        slots.push(`${start}:00 - ${start}:30`);
        if (i < 17) {
          slots.push(`${start}:30 - ${end}:00`);
        }
      }
      return slots;
    },
    addUnavailableSlot() {
      // Add the selected slot as an unavailable slot for the selected date
      if (!this.preventDate || !this.preventSlot) {
        alert('Please select a date and slot to prevent.');
        return;
      }
      const [startTime, endTime] = this.preventSlot.split(' - '); // Split the slot into start and end time
      const bookingDateTime = `${this.preventDate} ${startTime}`;
      if (this.isSlotUnavailable(bookingDateTime)) {
        alert('This slot is already marked as unavailable.');
        return;
      }
      // Send a request to add the unavailable slot
      const unavailableDateTime = `${this.preventDate} ${startTime}:00`;
      axios
        .post('/api/unavailable-slots', {
          unavailable_datetime: unavailableDateTime,
          start_time: startTime,
          end_time: endTime,
          booking_id: null, // Set booking_id as null for "reserved by owner" slots
        })
        .then(response => {
          alert('Slot marked as unavailable!');
          this.fetchUnavailableSlots(); // Refresh the list of unavailable slots
        })
        .catch(error => {
          console.error('Error marking slot as unavailable:', error);
        });
    },
    formatDate(datetime) {
      const date = new Date(datetime);
      const day = date.getDate().toString().padStart(2, '0');
      const month = (date.getMonth() + 1).toString().padStart(2, '0'); 
      const year = date.getFullYear();
      const hours = date.getHours().toString().padStart(2, '0');
      const minutes = date.getMinutes().toString().padStart(2, '0');
      const seconds = date.getSeconds().toString().padStart(2, '0');
      
      return `${day}-${month}-${year} ${hours}:${minutes}:${seconds}`;
    },
  },
};
</script>
