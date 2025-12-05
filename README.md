# HopeVillage (HV) - Recreational Facility Booking System

A comprehensive recreational facility booking system built with Laravel 11, Jetstream, Livewire, and TailwindCSS.

## Project Overview

HopeVillage is a multi-location recreational facility management system that allows administrators to manage locations, amenities, events, and programs while enabling members to book facilities, track activities, and earn points.

## Tech Stack

- **Laravel 11**
- **Jetstream** (Teams disabled)
- **Livewire 3**
- **TailwindCSS**
- **MySQL**
- **Endroid QR Code** (for QR code generation)

## Key Features

### For Administrators
- Manage multiple HV locations
- Add amenities to respective locations
- Configure activity types (entry, watch, book, play, dine, etc.)
- Create events & programs
- Configure point system for activities
- View analytics and foot traffic

### For Members
- Create account using WhatsApp number or email
- Account verification via WhatsApp/email code
- QR code scanning for facility entry
- Book amenities (basketball court, billiard pool, gym, etc.)
- Book function halls for events
- View and register for upcoming events/programs
- Track activities with point system

## Database Schema

### Core Tables
- **users** - User accounts (Admin/Member)
- **locations** - HV facility locations
- **amenities** - Facilities and function halls within locations
- **activity_types** - Types of activities (entry, watch, play, etc.)
- **point_system_configs** - Point configurations for activities

### Booking & Events
- **bookings** - Amenity and function hall reservations
- **events** - One-time events
- **programs** - Recurring programs (weekly, monthly)
- **event_registrations** - Member registrations for events/programs

### Activity Tracking
- **member_activities** - Log of all member activities
- **point_logs** - Point transactions/records
- **verification_codes** - Email/WhatsApp verification codes

## User Types

1. **Administrator (admin)**
   - Full system access
   - Manage locations, amenities, activities
   - Configure point system
   - Create events/programs
   - View analytics

2. **Standard Member (member)**
   - Book amenities
   - Register for events/programs
   - Track activities
   - View points

## Activity Tracking Examples

1. Member 'Enter' into 'Cebu City Sports Club' (location) → logs points
2. Member 'Play' billiard in 'Billiard Hall' (amenity) at 'Cebu City Sports Club' → logs points
3. Member 'Watch' basketball at 'Basketball Court' (amenity) at 'Paris City Sports Club' → logs points

<!-- ## Installation

1. Clone the repository
2. Install dependencies:
   ```bash
   composer install
   npm install
   ```

3. Copy environment file:
   ```bash
   cp .env.example .env
   ```

4. Generate application key:
   ```bash
   php artisan key:generate
   ```

5. Configure database in `.env`

6. Run migrations:
   ```bash
   php artisan migrate
   ```

7. Build assets:
   ```bash
   npm run build
   ``` -->

## Project Status

### ✅ Completed
- Laravel 11 project setup with Jetstream (Teams disabled)
- Livewire 3 integration
- TailwindCSS setup
- Complete database schema and migrations
- All models with relationships
- QR code library integration

### 🚧 In Progress
- Authentication system with WhatsApp/email support
- Verification code system

### 📋 Pending
- Admin interface (manage locations, amenities, activity types, events/programs, point system)
- Member interface (QR code scanning, activity tracking, bookings, event registration)
- QR code generation and scanning system
- Point system logging implementation
- Weekly calendar view for events/programs
- Analytics dashboard (foot traffic, popular amenities)

## Next Steps

1. Implement authentication with WhatsApp/email
2. Create verification code system
3. Build admin dashboard
4. Build member dashboard
5. Implement QR code scanning
6. Add point system logic
7. Create booking system
8. Build event/program calendar

## Directory Structure

```
app/
├── Models/
│   ├── User.php
│   ├── Location.php
│   ├── Amenity.php
│   ├── ActivityType.php
│   ├── PointSystemConfig.php
│   ├── Booking.php
│   ├── Event.php
│   ├── Program.php
│   ├── EventRegistration.php
│   ├── MemberActivity.php
│   ├── PointLog.php
│   └── VerificationCode.php
└── ...

database/migrations/
├── 2025_12_04_102007_modify_users_table_for_hv_system.php
├── 2025_12_04_102022_create_locations_table.php
├── 2025_12_04_102022_create_amenities_table.php
├── 2025_12_04_102022_create_activity_types_table.php
├── 2025_12_04_102022_create_point_system_configs_table.php
├── 2025_12_04_102039_create_bookings_table.php
├── 2025_12_04_102039_create_events_table.php
├── 2025_12_04_102039_create_programs_table.php
├── 2025_12_04_102039_create_event_registrations_table.php
├── 2025_12_04_102039_create_member_activities_table.php
├── 2025_12_04_102040_create_point_logs_table.php
└── 2025_12_04_102040_create_verification_codes_table.php
```

## License

This project is proprietary software for HopeVillage.
