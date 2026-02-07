![Drupal](https://img.shields.io/badge/Drupal-10-blue)
![PHP](https://img.shields.io/badge/PHP-8.x-purple)
![Custom Module](https://img.shields.io/badge/Type-Custom%20Module-green)
![No Contrib](https://img.shields.io/badge/Dependencies-None-success)

# Event Registration Module (Drupal 10)

A custom-built, production-ready Event Registration module for Drupal 10 that allows administrators to configure events and users to register for them through a dynamic AJAX-powered form.
The module is implemented without using nodes, views, or any contributed modules, relying entirely on Drupal core APIs and custom database tables.

---

## 🚀 Project Overview

This module is designed to solve a common real-world requirement in colleges and organizations — managing event registrations efficiently while maintaining performance, security, and scalability.

Unlike traditional Drupal implementations that depend on nodes and Views, this module uses a database-driven architecture, making it lightweight, fast, and suitable for enterprise-level Drupal applications.

---

## ✨ Features Overview

### 🔧 Admin Features
- Custom Event Configuration page
- Configurable registration **start and end dates**
- Email notification configuration
- Secure admin listing of registrations
- Filters by **event name** and **event date**
- CSV export of registration data
- Custom permission-based access control

### 👤 User Features
- Dynamic Event Registration Form
- AJAX-based dependent dropdowns
- Registration allowed only within valid date range
- Email confirmation on successful registration
- Strong server-side validation with user-friendly messages

---

## 🎯 Why This Module is Different

❌ No Nodes  
❌ No Views  
❌ No Contributed Modules  

✅ Custom Database Tables  
✅ Repository & Service-based Architecture  
✅ Drupal Mail API Integration  
✅ PSR-4 & Drupal Coding Standards  

This makes the module **production-ready**, not just an academic prototype.

---

## 🛠️ Technical Stack

- **Drupal Version:** 10.x  
- **PHP Version:** 8.x  
- **Database:** MySQL  
- **Architecture:** PSR-4, Dependency Injection  
- **APIs Used:** Form API, Config API, Mail API  
- **Coding Standards:** Drupal Coding Standards 

---

## 📁 Module Structure

```text
drupal-event-registration/
├── screenshots
│   ├── 01_module_structure.png
│   ├── 02_event_config_admin.png
│   ├── 03_registration_form.png
│   ├── 04_filled_form.png
│   ├── 05_admin_listing.png
│   └── 06_database_tables.png
├── composer.json
├── composer.lock
├── README.md
└── web/
    └── modules/
        └── custom/
            └── event_registration/
                ├── event_registration.info.yml
                ├── event_registration.module
                ├── event_registration.install
                ├── event_registration.permissions.yml
                ├── event_registration.routing.yml
                ├── event_registration.services.yml
                ├── sql/
                │   └── drupal10.sql
                └── src/
                    ├── Form/
                    |   ├── AdminListingFilterForm.php
                    |   ├── AdminSettingForm.php
                    │   ├── EventConfigForm.php
                    │   └── EventRegistrationForm.php
                    ├── Controller/
                    │   └── AdminListingController.php
                    ├── Repository/
                    │   └── EventRepository.php
                    ├── Service/
                    │   └── EventMAilService.php
                    └── Mail/
                        └── EventRegistrationMail.php
```

---

## ⚙️ Installation Steps

1. **Clone the repository**

   ```bash
   git clone https://github.com/dikshamitra/drupal-event-registration.git
   ```

2. **Place the module**

   ```text
   web/modules/custom/event_registration
   ```

3. **Import database tables**
   Import the SQL file:

   ```text
   web/modules/custom/event_registration/sql/drupal10.sql
   ```

4. **Enable the module**

   ```bash
   drush en event_registration
   ```

5. **Clear cache**

   ```bash
   drush cr
   ```

---

## 🔗 Important URLs

### Admin Pages

* Event Configuration
  `/admin/config/event-registration`

* Email Configuration
  `/admin/config/event-registration/email`

* Registration Listing
  `/admin/events/registrations`

* Export Registrations (CSV)
  `/admin/events/registrations/export`

### User Page

* Event Registration Form
  `/events/register`

---

## 🧩 Event Configuration (Admin)

Admins can configure events with the following fields:

* Event Name
* Category (Online Workshop, Hackathon, Conference, One-day Workshop)
* Event Date
* Registration Start Date
* Registration End Date

Stored in database table:

`event_registration_event`

---

## 📝 Event Registration Form (User)

The registration form is available only between the configured registration start and end dates.

### Fields

* Full Name
* Email Address
* College Name
* Department
* Event Category (AJAX)
* Event Date (AJAX)
* Event Name (AJAX)

### AJAX Behavior

* Event dates load based on selected category
* Event names load based on selected category and date

---

## ✅ Validation Rules

* Duplicate registration prevention using **Email + Event**

### Validations

* Proper email format
* No special characters in text fields
* User-friendly validation messages

---

## 🗄️ Database Tables

### `event_registration_event`

| Field       | Description                     |
|------------|---------------------------------|
| id         | Event ID                        |
| event_name | Event name                      |
| category   | Event category                  |
| event_date | Event date                      |
| reg_start  | Registration start date         |
| reg_end    | Registration end date           |
| created    | Timestamp                       |

---

### `event_registration_signup`

| Field      | Description        |
|-----------|--------------------|
| id        | Registration ID    |
| event_id  | Event reference    |
| full_name | Participant name   |
| email     | Participant email  |
| college   | College name       |
| department| Department         |
| created   | Timestamp          |

---

## 📧 Email Notifications

Implemented using **Drupal Mail API** and `hook_mail()`.

### Emails Sent

* User confirmation email
* Admin notification email (optional)

### Admin Configuration

* Admin email address
* Enable/disable admin notifications

Configuration stored using Config API:

`event_registration.settings`

---

## 📊 Admin Registration Listing

Accessible only to users with permission:

`View event registrations`

### Features

* Filter by Event Date
* Filter by Event Name
* Participant count display
* CSV export of filtered results

---

## 🔐 Permissions

Custom permission:

* **View event registrations**

Assign via:

`Admin → People → Permissions`

---

## 📸 Project Screenshots

### Module Structure
![Module Structure](screenshots/01_module_structure.png)

### Event Configuration (Admin)
![Admin Config](screenshots/02_event_config_admin.png)

### Event Registration Form
![Registration Form](screenshots/03_registration_form.png)

### Filled Registration Form
![Filled Form](screenshots/04_filled_form.png)

### Admin Registrations Listing
![Admin Listing](screenshots/05_admin_listing.png)

### Database Tables
![Database](screenshots/06_database_tables.png)

---

## 🔒 Security & Performance

- Strong server-side input validation  
- Secure admin routes protected via permissions  
- No sensitive data exposed in URLs  
- Custom database tables for faster queries  
- No Views or Node overhead  

---

## 🌍 Real-World Use Cases

- College and university event portals  
- Hackathons and technical workshops  
- Conferences and seminars  
- Corporate training programs  

---

## 👩‍💻 Author & Contact

**Diksha Mitra**

Email: dikshamitra3109@gmail.com
