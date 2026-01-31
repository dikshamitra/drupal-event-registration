📘 Event Registration Module (Drupal 10)


📌 Overview

The Event Registration module is a custom Drupal 10 module that allows administrators to configure events and enables users to register for those events through a custom form.
It stores registration data in custom database tables and provides an admin listing page with filtering, AJAX functionality, and CSV export.

This module is built without any contrib modules and follows Drupal 10 coding standards, PSR-4 autoloading, and Dependency Injection principles.


⚙️ Requirements

Drupal 10.x

PHP 8.1+

MySQL / MariaDB

Apache (XAMPP / LAMP / WAMP supported)


📥 Installation Steps

Clone or copy the module into:

web/modules/custom/event_registration


Enable the module:

Via UI:
Admin → Extend → Enable "Event Registration"

Or using Drush:

drush en event_registration -y


Import database tables:

Use the provided .sql file

Import it into the Drupal database using phpMyAdmin or MySQL CLI

Clear cache:

drush cr


🔗 Important URLs

🔧 Admin Pages
Feature	URL
Event Configuration	/admin/config/event-registration/events
Module Settings	/admin/config/event-registration/settings
Admin Registration Listing	/admin/event-registrations

🧑‍💻 User Page
Feature	URL
Event Registration Form	/event/register


🗂️ Database Tables

1️⃣ Event Configuration Table (event_config)

Stores event details configured by admin.

Fields:

id (Primary Key)

reg_start – Registration start date

reg_end – Registration end date

event_date

event_name

category

2️⃣ Event Registration Table (event_registration)

Stores user registration details.

Fields:

id (Primary Key)

full_name

email

college_name

department

category

event_date

event_id (Foreign key referencing event_config)

created (timestamp)


📝 Forms & Functionality

🛠️ Event Configuration Form (Admin)

Event Registration Start Date

Event Registration End Date

Event Date

Event Name

Event Category

🧾 Event Registration Form (User)

Available only between registration start & end date.

Fields:

Full Name

Email Address

College Name

Department

Category (from admin config)

Event Date (AJAX filtered)

Event Name (AJAX filtered)

✅ Validation Logic

Prevents duplicate registrations using:

Email + Event Date


Validates:

Email format

No special characters in text fields

Displays user-friendly error messages

📧 Email Logic

Uses Drupal Mail API

Supports:

User confirmation email

Optional admin notification

Email content includes:

Name

Event Date

Event Name

Category

Admin notifications can be enabled/disabled via configuration

⚙️ Configuration Page

Admin can configure:

Admin notification email address

Enable/disable admin notifications

✔ Uses Drupal Config API
✔ No hard-coded values

📊 Admin Listing Page

Accessible only to users with a custom permission

Features:

Event Date dropdown (AJAX)

Event Name dropdown (AJAX)

Total participants count

Dynamic table update

CSV export option

Displayed Fields:

Name

Email

Event Date

College Name

Department

Submission Date


🧩 Technical Highlights

Drupal Form API

Custom database tables

AJAX callbacks

Dependency Injection

PSR-4 autoloading

No use of \Drupal::service() in business logic


📁 Module Structure (Key Files)
event_registration/
├── sql/
│   └── drupal10.sql
│
├── src/
│   ├── Controller/
│   │   └── AdminListingController.php
│   │
│   ├── Form/
│   │   ├── AdminListingFilterForm.php
│   │   ├── AdminSettingsForm.php
│   │   ├── EventConfigForm.php
│   │   └── EventRegistrationForm.php
│   │
│   ├── Mail/
│   │   └── EventRegistrationMail.php
│   │
│   ├── Repository/
│   │   └── EventRepository.php
│   │
│   └── Service/
│       └── EventMailService.php
│
├── event_registration.info.yml
├── event_registration.install
├── event_registration.module
├── event_registration.permissions.yml
├── event_registration.routing.yml
├── event_registration.services.yml
└── README.md

📌 Folder Explanation

Controller/

Handles admin listing page rendering and data display.

Form/

Contains all admin and user-facing forms.

Mail/

Defines email templates for user and admin notifications.

Repository/

Handles database queries and data fetching logic.

Service/

Contains reusable services like email handling.

sql/

Contains database dump for custom tables.


✅ Submission Checklist

✔ Custom module directory

✔ Database .sql file

✔ README.md

✔ Clean & readable code

✔ Multiple GitHub commits


👤 Author

Name: Diksha Mitra
Email: dikshamitra3109@gmail.com
Project: Web Development Screening Task
Drupal Version: 10.x