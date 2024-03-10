# Medical Clinic Management Application

## Project Overview
This project aims to develop a medical practice management application that enables the secretarial staff to input consultation appointments. It is designed to manage the list of users of the center (with their titles, full names, addresses, birth dates and places, and social security numbers) as well as the list of doctors (with their titles, last names, and first names). Each user can have a referring doctor from the center. The secretariat should be able to input appointments by selecting the user and the doctor from lists and entering the date, time, and duration of the consultation (defaulted to 30 minutes).

## Goal
The application should be user-friendly and accessible to beginners, designed with daily use in mind.

## Developed By
- Emad AL RAMIMI
- Nassim Khoujane

## Development Approach
The project is developed using the MVC (Model-View-Controller) methodology to ensure clear separation of concerns, ease of maintenance, and scalability.

## Features
- **User Management**: Display, add, modify, and delete users.
- **Doctor Management**: Display, add, modify, and delete doctors.
- **Consultation Management**: Display and input of consultations, with chronological sorting. Default to referring doctor if associated with a user.
- **Statistics Page**: Display statistics including user distribution by sex and age, and total duration of consultations by each doctor.
- **Application Framework**: Secure the application with an authentication page. Implement a global menu for easy navigation.
- **Planning**: Manage consultations with features for modification, deletion, non-overlapping checks for the same doctor, and filtering by doctor.
- **Styling and Usability**: Use CSS and software ergonomics principles for a pleasant and intuitive user experience.

## Exercises
1. **Data Model**: Design and validate the data model, then create the corresponding MySQL database.
2. **User and Doctor Management**: Implement pages for the management of users and doctors.
3. **Consultation Input**: Develop pages for consultation management.
4. **Statistics**: Create a page for displaying statistics.
5. **Application Framework**: Secure the application and implement a global navigation menu.
6. **Planning Enhancements**: Add features for editing, deleting, and controlling consultations scheduling.
7. **Styling**: Apply CSS and ergonomics principles for improved usability.

## Getting Started
- The homepage is named `index.php` or `index.html`.
- Utilize `include` or `require` for code modularization (e.g., database connection, user authentication, menu display).
- Dates are inputted and displayed in the French format (dd/mm/yyyy), with the format explicitly stated.

## Requirements
- PHP 7.x or higher
- MySQL Database
- Web Server (e.g., Apache, Nginx)

## Installation
1. Clone the repository or download the source code.
2. Configure the web server to point to the project directory.
3. Import the SQL schema into your MySQL database.
4. Modify the `config.php` file with your database connection details.
5. Access the project through your web browser by navigating to the project's URL.

## Contributing
Contributions are welcome. Please open an issue or submit a pull request for any improvements or bug fixes.

## License
This project is licensed under the MIT License.
