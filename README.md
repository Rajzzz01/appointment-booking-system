# 🗓️ Appointment Booking System

The **Appointment Booking System** is a web-based application built with **Laravel (Backend) and Vue.js (Frontend)**.  
It allows users to **book, manage, and cancel appointments** while handling reminders and email notifications.

---

## 🚀 Features

✅ **User Authentication** (Login/Register with Laravel Sanctum)  
✅ **Book Appointments** (With Guests & Timezone Support)  
✅ **Cancel Appointments** (Only if more than 30 minutes left)  
✅ **Email Notifications** (Booking, Cancellation, Reminders)  
✅ **Sorting & Filtering** (By Created Date & Upcoming)  
✅ **Queue-based Email Sending** (Using Laravel Queues)  
✅ **Universal Timezone Handling** (Displays User’s Local Time)

---

## 🛠️ Tech Stack

| Technology         | Description                 |
| ------------------ | --------------------------- |
| **Laravel 10**     | Backend Framework           |
| **Vue.js 3**       | Frontend Framework          |
| **MySQL**          | Database                    |
| **Inertia.js**     | Laravel-Vue Bridge          |
| **PHPUnit**        | Testing Frameworks          |
| **Laravel Queues** | Asynchronous Email Handling |

---

## 📦 Installation Guide

### **🔹 1. Clone the Repository**

```sh
git clone https://github.com/Rajzzz01/appointment-booking-system.git
cd appointment-booking-system
```

### **🔹 2. Install Backend Dependencies**

```sh
composer install
```

### **🔹 3. Install Frontend Dependencies**

```sh
npm install
```

### **🔹 4. Set Up Environment Variables**

```sh
cp .env.example .env

Edit .env and configure:
    Database (DB_DATABASE, DB_USERNAME, DB_PASSWORD)
    Mail Settings (MAIL_MAILER, MAIL_HOST, MAIL_PORT, etc.)
    Queue Driver (QUEUE_CONNECTION=database)
```

### **🔹 5. Generate Application Key**

```sh
php artisan key:generate
```

### **🔹 6. Serve the Application**

```sh
php artisan serve
npm run dev
```

## ✅ Running Tests

### **🔹 1. Run Test Cases**

```sh
php artisan test --filter=AppointmentTest
```
