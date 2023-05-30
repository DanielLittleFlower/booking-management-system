# Booking Management System Documentation

## Introduction
The Booking Management System is a web application designed to facilitate the booking of appointments for a service. It allows users to view available time slots, make bookings, and manage bookings. The system also provides an interface for administrators to view and manage bookings, as well as mark certain time slots as unavailable.

## Installation
To install and set up the Booking Management System, follow these steps:

1. Clone the repository from GitHub: `git clone https://github.com/DanielLittleFlower/`
2. Navigate to the project directory: `cd booking-management-system`
3. Install the dependencies: `npm install`
4. Create a copy of the `.env.example` file and rename it to `.env`: `cp .env.example .env`
5. Configure the database connection in the `.env` file with your database credentials.
6. Run database migrations: `php artisan migrate`
7. Start the development server: `php artisan serve / npm run dev`
8. Access the application in your web browser at `http://127.0.0.0:8000`

## Usage
The Booking Management System provides the following features:

1. Calendar View: The Calendar view displays the available time slots for booking appointments. Users can navigate between weeks, select a time slot, and make a booking.

2. Booking Form: When a user selects a time slot, a booking form is displayed, prompting the user to enter their details (name, email, phone number, vehicle make, and vehicle model) for the appointment. Upon submission, a booking confirmation is sent to the user and the administrator.

3. Upcoming Bookings: The Upcoming Bookings view displays a table of all the bookings made, including the customer's details and the date and time of the appointment. Administrators can delete bookings from this view.

4. Prevent Specific Days and Slots: Administrators can mark specific days and slots as unavailable for booking, preventing users from selecting them.

## API Endpoints
The Booking Management System provides the following API endpoints:

- `GET /api/bookings`: Retrieves all bookings.
- `POST /api/booking`: Creates a new booking.
- `DELETE /api/bookings/{id}`: Deletes a booking with the specified ID.
- `GET /api/unavailable-slots`: Retrieves all unavailable slots.
- `POST /api/unavailable-slots`: Creates a new unavailable slot.

## Frontend Components
The Booking Management System frontend utilizes the following Vue components:

1. `Calendar.vue`: This component displays a calendar view of available time slots and allows users to make bookings by selecting a time slot.

2. `AdminInterface.vue`: This component provides an interface for administrators to view and manage bookings, as well as mark certain time slots as unavailable.

For a deeper understanding of each component's functionality and structure, refer to the corresponding component files.

## Backend Controllers
The Booking Management System backend includes the following controllers:

1. `BookingController`: This controller handles the creation, retrieval, updating, and deletion of bookings. It also sends booking confirmation emails to the user and administrator.

2. `UnavailableSlotController`: This controller manages the marking of specific time slots as unavailable for booking.

The controller files contain detailed comments explaining the purpose and functionality of each method.

## Database Schema
The Booking Management System utilizes a database with the following tables:

- `bookings`: Stores booking details such as the customer's name, email, phone number, vehicle make, vehicle model, and the date and time of the appointment.

- `unavailable_slots`: Records unavailable time slots, including the date and time, start time, end time, and the corresponding booking ID.


## Deployment
To deploy the Booking Management System to a production environment, follow these general steps:

1. Set up the web server and configure the necessary server software.
2. Configure the web server to point to the application's public directory.
3. Set up the database server and create a new database for the application.
4. Update the `.env` file with the appropriate database connection details.
5. Run the necessary deployment scripts or commands (e.g., build the frontend assets, optimize the application, etc.).
6. Ensure the appropriate file permissions are set for security.
7. Configure any additional environment-specific settings (e.g., caching, logging, etc.).
8. Start the web server and access the application using the assigned domain or IP address.

Please consult your specific web server and deployment environment documentation for detailed instructions.

## Troubleshooting
If you encounter any issues while using the Booking Management System, try the following troubleshooting steps:

1. Check the application and server logs for error messages or stack traces.
2. Verify that the database connection settings in the `.env` file are correct.
3. Ensure that the necessary PHP extensions and dependencies are installed.
4. Clear the application cache and regenerate any necessary configuration files.
5. Double-check that the required database tables and migrations are up to date.
6. Review the application and server configuration files for any potential misconfigurations.


## Conclusion
The Booking Management System provides a user-friendly interface for managing appointments and bookings. By utilizing the calendar view, users can easily select available time slots and make appointments. Administrators have access to additional features for managing bookings and marking time slots as unavailable. We hope this documentation has provided a comprehensive overview of the application and its functionality.

Please note that this is a draft, and you may need to make adjustments and additions based on your specific application requirements and architecture.
