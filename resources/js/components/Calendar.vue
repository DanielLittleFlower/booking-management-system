<template>
    <div class="flex flex-col max-w-full mx-auto mt-10 px-4 py-8 rounded-lg shadow-md bg-white">
      <div class="flex justify-between">
        <button class="py-2 px-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded" @click="prevWeek">Previous Week</button>
        <button class="py-2 px-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded" @click="nextWeek">Next Week</button>
      </div>
      <div class="grid grid-cols-3 gap-6 mt-6">
        <div v-for="day in currentWeekDates" :key="day" class="p-4 border rounded-lg">
          <h2 class="text-2xl font-bold mb-4">{{ dayNames[day.getDay()] }} - {{ formatDate(day) }}</h2>
          <div v-for="slot in slots" :key="slot" class="time-slot cursor-pointer px-4 py-2 mb-2"
            :class="{
                'bg-green-300 text-white-700': !isUnavailable(day, slot),
                'bg-red-500 text-white': isUnavailable(day, slot)
            }"
            @click="selectSlot(day, slot)"
            >
            {{ slot }}
          </div>
        </div>
      </div>
      <div v-if="selectedSlot" class="fixed inset-0 flex items-center justify-center z-10">
        <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
        <div class="modal-container bg-white w-96 mx-auto rounded-lg shadow-lg z-50 overflow-y-auto">
          <div class="modal-content py-4 px-6">
            <h2 class="text-2xl font-bold mb-4">Book Slot</h2>
            <form @submit.prevent="submitForm" class="flex flex-col space-y-4">
              <label class="font-semibold text-lg">
                Name
                <input 
                  type="text" 
                  v-model="bookingData.name" 
                  @input="validateForm" 
                  required 
                  :class="{
                    'border-red-500': errors.name, 
                    'border-gray-300': !errors.name, 
                    'focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500': true 
                  }"
                  class="mt-1 p-2 w-full rounded"
                />
                <span v-if="errors.name" class="text-red-500">{{ errors.name }}</span>
              </label>
              
              <label class="font-semibold text-lg">
                Email
                <input 
                  type="email" 
                  v-model="bookingData.email" 
                  @input="validateForm" 
                  required 
                  :class="{
                    'border-red-500': errors.email, 
                    'border-gray-300': !errors.email, 
                    'focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500': true 
                  }"
                  class="mt-1 p-2 w-full rounded"
                />
                <span v-if="errors.email" class="text-red-500">{{ errors.email }}</span>
              </label>
              
              <label class="font-semibold text-lg">
                Phone Number
                <input 
                  type="text" 
                  v-model="bookingData.phone_number" 
                  @input="validateForm" 
                  required 
                  :class="{
                    'border-red-500': errors.phone_number, 
                    'border-gray-300': !errors.phone_number, 
                    'focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500': true 
                  }"
                  class="mt-1 p-2 w-full rounded"
                />
                <span v-if="errors.phone_number" class="text-red-500">{{ errors.phone_number }}</span>
              </label>
              
              <label class="font-semibold text-lg">
                Vehicle Make
                <input 
                  type="text" 
                  v-model="bookingData.vehicle_make" 
                  @input="validateForm" 
                  required 
                  :class="{
                    'border-red-500': errors.vehicle_make, 
                    'border-gray-300': !errors.vehicle_make, 
                    'focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500': true 
                  }"
                  class="mt-1 p-2 w-full rounded"
                />
                <span v-if="errors.vehicle_make" class="text-red-500">{{ errors.vehicle_make }}</span>
              </label>
              
              <label class="font-semibold text-lg">
                Vehicle Model
                <input 
                  type="text" 
                  v-model="bookingData.vehicle_model" 
                  @input="validateForm" 
                  required 
                  :class="{
                    'border-red-500': errors.vehicle_model, 
                    'border-gray-300': !errors.vehicle_model, 
                    'focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500': true 
                  }"
                  class="mt-1 p-2 w-full rounded"
                />
                <span v-if="errors.vehicle_model" class="text-red-500">{{ errors.vehicle_model }}</span>
              </label>
    
              <div class="flex justify-end mt-6">
                <button type="submit" class="py-2 px-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded">Book</button>
                <button class="ml-2 py-2 px-4 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded" @click="selectedSlot = null">Cancel</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    data() {
      return {
        bookingData: {
          name: '',
          email: '',
          phone_number: '',
          vehicle_make: '',
          vehicle_model: '',
          booking_datetime: '',
        },
        selectedSlot: null,
        dayNames: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
        slots: this.createSlots(),
        errors: {},
        currentWeekDates: [],
        unavailableSlots: [],
      };
    },
    created() {
        this.fetchUnavailableSlots();
        this.computeWeek(new Date());
    },
    methods: {
      createSlots() {
        const slots = [];
        for (let i = 9; i < 18; i++) {
            const start = i.toString().padStart(2, '0'); // Pad single-digit hour with leading zero
            const end = (i + 1).toString().padStart(2, '0'); 
            slots.push(`${start}:00 - ${start}:30`);
            if (i < 17) {
            slots.push(`${start}:30 - ${end}:00`);
            }
        }
        return slots;
      },
      fetchUnavailableSlots() {
        axios.get('/api/unavailable-slots')
          .then(response => {
            this.unavailableSlots = response.data;
          })
          .catch(error => {
            console.log(error);
          });
      },
      isUnavailable(day, slot) {
        const dateTime = this.computeDateTime(day, slot);
        return this.unavailableSlots.some(slot => {
            const unavailableDateTime = new Date(slot.unavailable_datetime);
            return +unavailableDateTime === +new Date(dateTime);
        });
      },
      selectSlot(day, slot) {
        if (!this.isUnavailable(day, slot)) {
          this.selectedSlot = { day, slot };
          this.bookingData.booking_datetime = this.computeDateTime(day, slot);
        }
      },
      submitForm() {
        if (this.validateForm()) {
          axios.post('/api/booking', this.bookingData)
            .then(response => {
              alert('Booking successful!');
              this.resetForm();
              this.selectedSlot = null;
              // Reload the unavailable slots
              this.fetchUnavailableSlots();
            })
            .catch(error => {
                if (error.response && error.response.status === 400) {
                alert('This slot is unavailable. Please choose a different slot.');
                } else {
                alert('An error occurred: ' + error);
                }
            });
        }
      },
      resetForm() {
        this.bookingData = {
          name: '',
          email: '',
          phone_number: '',
          vehicle_make: '',
          vehicle_model: '',
          booking_datetime: '',
        };
        this.errors = {};
      },
      prevWeek() {
        const newWeek = new Date(this.currentWeekDates[0]);
        newWeek.setDate(newWeek.getDate() - 7);
        this.computeWeek(newWeek);
      },
      nextWeek() {
        const newWeek = new Date(this.currentWeekDates[0]);
        newWeek.setDate(newWeek.getDate() + 7);
        this.computeWeek(newWeek);
      },
      computeDateTime(day, slot) {
        let [start, end] = slot.split(' - ');
        let [hour, minute] = start.split(':').map(Number);
        let date = new Date(day);
        date.setHours(hour, minute, 0, 0);
        // offset the date by the timezone offset
        let timezoneOffset = date.getTimezoneOffset() * 60000;
        let offsetDate = new Date(date.getTime() - timezoneOffset);
        let datetime = offsetDate.toISOString().slice(0, 16);
        return offsetDate.toISOString().slice(0, 16);
      },
      computeWeek(currentDate) {
        let currentDay = currentDate.getDay();
        let difference = currentDay === 0 ? 0 : currentDay - 1;
        let startDate = new Date(currentDate);
        startDate.setDate(startDate.getDate() - difference);
        this.currentWeekDates = Array.from({ length: 5 }, (_, i) => {
          let date = new Date(startDate);
          date.setDate(date.getDate() + i);
          return date;
        });
      },
      formatDate(date) {
        return `${date.getDate()}-${date.getMonth() + 1}-${date.getFullYear()}`;
      },
      validateForm() {
        this.errors = {};
  
        if (!this.bookingData.name) {
          this.errors.name = 'Name is required.';
        }
  
        if (!this.bookingData.email) {
          this.errors.email = 'Email is required.';
        } else if (!this.isValidEmail(this.bookingData.email)) {
          this.errors.email = 'Please enter a valid email address.';
        }
  
        if (!this.bookingData.phone_number) {
          this.errors.phone_number = 'Phone number is required.';
        }
  
        if (!this.bookingData.vehicle_make) {
          this.errors.vehicle_make = 'Vehicle make is required.';
        }
  
        if (!this.bookingData.vehicle_model) {
          this.errors.vehicle_model = 'Vehicle model is required.';
        }
  
        return Object.keys(this.errors).length === 0;
      },
      isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
      },
    },
  };
  </script>
  
