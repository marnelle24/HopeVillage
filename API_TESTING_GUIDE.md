# HopeVillage API Testing Guide

This guide provides step-by-step instructions for testing all API endpoints in Postman.

## Base URL

```
http://localhost:8000/api
```

Or if using a different domain:
```
https://hopevillage.sg/api
```

---

## Table of Contents

1. [Member Activity Scan](#1-member-activity-scan)
2. [Get Member Activities](#2-get-member-activities)
3. [Get Activity Types](#3-get-activity-types)
4. [Get Locations](#4-get-locations)
5. [Get Events](#5-get-events)
6. [Get Settings](#6-get-settings)
7. [Event Registration Scan](#7-event-registration-scan)

---

## 1. Member Activity Scan

**Endpoint:** `POST /api/member-activity/scan`

**Authentication:** Not Required (Public Endpoint)

**Description:** Records a member activity when scanning QR codes. Awards points for ENTRY activities.

### Step-by-Step Instructions:

1. **Open Postman** and create a new request
2. **Set Method:** POST
3. **Enter URL:** `https://hopevillage.sg/api/member-activity/scan`
4. **Set Headers:**
   - `Content-Type`: `application/json`
   - `Accept`: `application/json`
5. **Go to Body tab:**
   - Select **raw**
   - Select **JSON** from dropdown
   - Enter the following JSON:

```json
{
    "qr_code": "ABC123XYZ",
    "member_fin": "123W",
    "location_code": "LOC001",
    "type_of_activity": "ENTRY"
}
```

6. **Click Send**

### Request Parameters:

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `qr_code` | string | Yes | QR code string (max 255 characters) |
| `member_fin` | string | No | Member's FIN (must exist in users table) |
| `location_code` | string | Yes | Location code (must exist in locations table) |
| `type_of_activity` | string | Yes | Activity type (e.g., "ENTRY", "EXIT") |

### Expected Response (201 Created):

```json
{
    "success": true,
    "message": "Activity recorded successfully",
    "data": {
        "member": {
            "fin": "123W",
            "name": "John Doe",
            "total_points": 60
        },
        "location": {
            "code": "LOC001",
            "name": "Main Location"
        },
        "activity": {
            "id": 1,
            "type": "ENTRY",
            "activity_time": "2024-01-15T10:30:00+00:00"
        },
        "points_awarded": 10
    }
}
```

### Error Responses:

**Member Not Found (404):**
```json
{
    "success": false,
    "message": "Member not found",
    "error": "No member found with FIN: 999Z"
}
```

**Location Not Found (404):**
```json
{
    "success": false,
    "message": "Location not found",
    "error": "No location found with code: INVALID"
}
```

**User Not a Member (422):**
```json
{
    "success": false,
    "message": "User is not a member",
    "error": "User with FIN 123W is not a member (user_type: admin)"
}
```

**Time Gap Restriction (422):**
```json
{
    "success": false,
    "message": "it requires a 3600 second(s) gap to scan again.",
    "error": "Member has a recent entry at this location within the time gap"
}
```

### Testing Scenarios:

1. **Test ENTRY activity** - Should award points
2. **Test EXIT activity** - Should not award points
3. **Test with invalid member_fin** - Should return 404
4. **Test with invalid location_code** - Should return 404
5. **Test duplicate ENTRY within time gap** - Should return 422

---

## 2. Get Member Activities

**Endpoint:** `GET /api/member-activities`

**Authentication:** Not Required

**Description:** Returns all member activities with optional filtering by activity type or member QR code.

### Step-by-Step Instructions:

#### Option A: Get All Member Activities

1. **Open Postman** and create a new request
2. **Set Method:** GET
3. **Enter URL:** `https://hopevillage.sg/api/member-activities`
4. **Set Headers:**
   - `Accept`: `application/json`
5. **Click Send**

#### Option B: Filter by Activity Type

1. **Open Postman** and create a new request
2. **Set Method:** GET
3. **Enter URL:** `https://hopevillage.sg/api/member-activities?activity_type_id=1`
4. **Set Headers:**
   - `Accept`: `application/json`
5. **Click Send**

#### Option C: Filter by Member QR Code

1. **Open Postman** and create a new request
2. **Set Method:** GET
3. **Enter URL:** `https://hopevillage.sg/api/member-activities?qr_code=ABC123XYZ`
4. **Set Headers:**
   - `Accept`: `application/json`
5. **Click Send**

#### Option D: Combine Filters

1. **Open Postman** and create a new request
2. **Set Method:** GET
3. **Enter URL:** `https://hopevillage.sg/api/member-activities?activity_type_id=1&qr_code=ABC123XYZ&per_page=20`
4. **Set Headers:**
   - `Accept`: `application/json`
5. **Click Send**

### Query Parameters:

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `activity_type_id` | integer | No | Filter by specific activity type ID |
| `qr_code` | string | No | Filter by member QR code |
| `per_page` | integer | No | Number of results per page (default: 15) |

### Example URLs:

- Get all activities: `https://hopevillage.sg/api/member-activities`
- Get activities by type: `https://hopevillage.sg/api/member-activities?activity_type_id=1`
- Get activities by member: `https://hopevillage.sg/api/member-activities?qr_code=ABC123XYZ`
- Get activities with pagination: `https://hopevillage.sg/api/member-activities?per_page=20`
- Combine filters: `https://hopevillage.sg/api/member-activities?activity_type_id=1&qr_code=ABC123XYZ`

### Expected Response (200 OK):

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "user_id": 1,
            "activity_type_id": 1,
            "location_id": 1,
            "amenity_id": null,
            "activity_time": "2024-01-15T10:30:00.000000Z",
            "description": "Member ENTRY at Main Location",
            "metadata": {
                "scanned_at": "2024-01-15T10:30:00+00:00",
                "location_code": "LOC001",
                "member_fin": "123W",
                "qr_code": "ABC123XYZ",
                "device_info": "Mozilla/5.0...",
                "ip_address": "127.0.0.1"
            },
            "created_at": "2024-01-15T10:30:00.000000Z",
            "updated_at": "2024-01-15T10:30:00.000000Z",
            "user": {
                "id": 1,
                "name": "John Doe",
                "email": "john@example.com",
                "fin": "123W",
                "qr_code": "ABC123XYZ",
                "user_type": "member",
                "total_points": 60
            },
            "activity_type": {
                "id": 1,
                "name": "ENTRY",
                "description": "Entry activity",
                "is_active": true
            },
            "location": {
                "id": 1,
                "location_code": "LOC001",
                "name": "Main Location",
                "address": "123 Main St",
                "is_active": true
            },
            "amenity": null,
            "point_log": {
                "id": 1,
                "points": 10,
                "awarded_at": "2024-01-15T10:30:00.000000Z"
            }
        }
    ],
    "pagination": {
        "current_page": 1,
        "last_page": 5,
        "per_page": 15,
        "total": 75,
        "from": 1,
        "to": 15
    }
}
```

### Error Response (500 Internal Server Error):

```json
{
    "success": false,
    "message": "Failed to fetch member activities",
    "error": "Internal server error"
}
```

### Testing Scenarios:

1. **Test get all activities** - Should return paginated list of all activities
2. **Test filter by activity_type_id** - Should return only activities of that type
3. **Test filter by qr_code** - Should return only activities for that member
4. **Test combine filters** - Should return activities matching both filters
5. **Test pagination** - Should respect per_page parameter
6. **Test with invalid activity_type_id** - Should return empty results
7. **Test with invalid qr_code** - Should return empty results

---

## 3. Get Activity Types

**Endpoint:** `GET /api/activity-types`

**Authentication:** Not Required

**Description:** Returns all active activity types.

### Step-by-Step Instructions:

1. **Open Postman** and create a new request
2. **Set Method:** GET
3. **Enter URL:** `https://hopevillage.sg/api/activity-types`
4. **Set Headers:**
   - `Accept`: `application/json`
5. **Click Send**

### Expected Response (200 OK):

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "ENTRY",
            "description": "Entry activity",
            "is_active": true,
            "created_at": "2024-01-15T10:00:00.000000Z",
            "updated_at": "2024-01-15T10:00:00.000000Z"
        },
        {
            "id": 2,
            "name": "EXIT",
            "description": "Exit activity",
            "is_active": true,
            "created_at": "2024-01-15T10:00:00.000000Z",
            "updated_at": "2024-01-15T10:00:00.000000Z"
        }
    ]
}
```

---

## 4. Get Locations

**Endpoint:** `GET /api/locations`

**Authentication:** Not Required

**Description:** Returns all active locations.

### Step-by-Step Instructions:

1. **Open Postman** and create a new request
2. **Set Method:** GET
3. **Enter URL:** `https://hopevillage.sg/api/locations`
4. **Set Headers:**
   - `Accept`: `application/json`
5. **Click Send**

### Expected Response (200 OK):

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "location_code": "LOC001",
            "name": "Main Location",
            "address": "123 Main St",
            "city": "City Name",
            "province": "Province Name",
            "postal_code": "12345",
            "is_active": true,
            "created_at": "2024-01-15T10:00:00.000000Z",
            "updated_at": "2024-01-15T10:00:00.000000Z"
        }
    ]
}
```

---

## 5. Get Events

**Endpoint:** `GET /api/events`

**Authentication:** Not Required

**Description:** Returns all active/published events with optional filtering.

### Step-by-Step Instructions:

1. **Open Postman** and create a new request
2. **Set Method:** GET
3. **Enter URL:** `https://hopevillage.sg/api/events`
4. **Set Headers:**
   - `Accept`: `application/json`
5. **Optional Query Parameters:**
   - Add query parameters in the URL or use the Params tab:
     - `status`: Filter by status (default: "published")
     - `location_id`: Filter by location ID
     - `search`: Search in title, description, venue, or location name
     - `upcoming`: Only show upcoming events (default: true) - use `true` or `false`
     - `limit`: Limit number of results
6. **Click Send**

### Query Parameters:

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `status` | string | No | Event status (default: "published") |
| `location_id` | integer | No | Filter by location ID |
| `search` | string | No | Search in title, description, venue, location name |
| `upcoming` | boolean | No | Only upcoming events (default: true) |
| `limit` | integer | No | Limit number of results |

### Example URLs:

- Get all upcoming events: `https://hopevillage.sg/api/events`
- Get events by location: `https://hopevillage.sg/api/events?location_id=1`
- Search events: `https://hopevillage.sg/api/events?search=basketball`
- Get all events (including past): `https://hopevillage.sg/api/events?upcoming=false`
- Limit results: `https://hopevillage.sg/api/events?limit=5`

### Expected Response (200 OK):

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "event_code": "EVT001",
            "title": "Basketball Tournament",
            "description": "Annual basketball tournament",
            "venue": "Main Court",
            "start_date": "2024-02-01T10:00:00+00:00",
            "end_date": "2024-02-01T18:00:00+00:00",
            "max_participants": 50,
            "status": "published",
            "thumbnail_url": "https://example.com/image.jpg",
            "registrations_count": 25,
            "location": {
                "id": 1,
                "location_code": "LOC001",
                "name": "Main Location",
                "address": "123 Main St",
                "city": "City Name",
                "province": "Province Name",
                "postal_code": "12345"
            }
        }
    ],
    "count": 1
}
```

---

## 6. Get Settings

**Endpoint:** `GET /api/settings`

**Authentication:** Not Required

**Description:** Returns all settings or a specific setting by key.

### Step-by-Step Instructions:

#### Option A: Get All Settings

1. **Open Postman** and create a new request
2. **Set Method:** GET
3. **Enter URL:** `https://hopevillage.sg/api/settings`
4. **Set Headers:**
   - `Accept`: `application/json`
5. **Click Send**

#### Option B: Get Specific Setting by Key

1. **Open Postman** and create a new request
2. **Set Method:** GET
3. **Enter URL:** `https://hopevillage.sg/api/settings?key=point_system_enabled`
4. **Set Headers:**
   - `Accept`: `application/json`
5. **Click Send**

### Query Parameters:

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `key` | string | No | Setting key to retrieve (if empty, returns all settings) |

### Expected Response - All Settings (200 OK):

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "key": "point_system_enabled",
            "value": "1",
            "description": "Enable or disable the point system",
            "created_at": "2024-01-15T10:00:00.000000Z",
            "updated_at": "2024-01-15T10:00:00.000000Z"
        },
        {
            "id": 2,
            "key": "entry_time_gap",
            "value": "3600",
            "description": "Time gap in seconds between entry scans",
            "created_at": "2024-01-15T10:00:00.000000Z",
            "updated_at": "2024-01-15T10:00:00.000000Z"
        }
    ]
}
```

### Expected Response - Specific Setting (200 OK):

```json
{
    "success": true,
    "data": {
        "id": 1,
        "key": "point_system_enabled",
        "value": "1",
        "description": "Enable or disable the point system",
        "created_at": "2024-01-15T10:00:00.000000Z",
        "updated_at": "2024-01-15T10:00:00.000000Z"
    }
}
```

### Error Response - Setting Not Found (404):

```json
{
    "success": false,
    "message": "Setting not found",
    "error": "No setting found with key: invalid_key"
}
```

---

## 7. Event Registration Scan

**Endpoint:** `POST /api/event-registration/scan`

**Authentication:** Not Required (Public Endpoint)

**Description:** Records member attendance at an event when scanning event QR codes using an external scanner. Creates or updates an event registration with status `attended`, sets `attended_at` to the current timestamp, and sets `type` to `external_scanner`.

### Step-by-Step Instructions:

1. **Open Postman** and create a new request
2. **Set Method:** POST
3. **Enter URL:** `https://hopevillage.sg/api/event-registration/scan`
4. **Set Headers:**
   - `Content-Type`: `application/json`
   - `Accept`: `application/json`
5. **Go to Body tab:**
   - Select **raw**
   - Select **JSON** from dropdown
   - Enter the following JSON:

```json
{
    "event_code": "EVT-XXXXX",
    "qr_code": "ABC123XYZ"
}
```

6. **Click Send**

### Request Parameters:

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `event_code` | string | Yes | Event's unique code (scanned from event QR code, max 255 characters) |
| `qr_code` | string | Yes | Member's QR code (scanned from member QR code, max 255 characters) |

### Expected Response (201 Created):

```json
{
    "success": true,
    "message": "Member attendance recorded successfully",
    "data": {
        "member": {
            "fin": "123W",
            "qr_code": "ABC123XYZ",
            "name": "John Doe"
        },
        "event": {
            "id": 1,
            "code": "EVT-XXXXX",
            "title": "Basketball Tournament"
        },
        "registration": {
            "id": 1,
            "status": "attended",
            "type": "external_scanner",
            "attended_at": "2024-01-15T10:30:00+00:00"
        }
    }
}
```

### Error Responses:

**Event Not Found (404):**
```json
{
    "success": false,
    "message": "Event not found",
    "error": "No event found with code: EVT-INVALID"
}
```

**Event Not Active (422):**
```json
{
    "success": false,
    "message": "Event is not active",
    "error": "Event 'Basketball Tournament' (code: EVT-XXXXX) is not active"
}
```

**Member Not Found (404):**
```json
{
    "success": false,
    "message": "Member not found",
    "error": "No member found with QR code: INVALID-QR-CODE"
}
```

**User Not a Member (422):**
```json
{
    "success": false,
    "message": "User is not a member",
    "error": "User with QR code ABC123XYZ is not a member (user_type: admin)"
}
```

**Validation Error (422):**
```json
{
    "message": "The event code field is required.",
    "errors": {
        "event_code": ["The event code field is required."]
    }
}
```

**Server Error (500):**
```json
{
    "success": false,
    "message": "Failed to record attendance",
    "error": "Internal server error"
}
```

### Important Notes:

- The event code is automatically normalized (trimmed and uppercased) before lookup
- If a registration already exists for the member and event, it will be updated to `attended` status with `external_scanner` type
- The `attended_at` timestamp is set to the current time when the scan occurs
- The registration type is always set to `external_scanner` for scans via this API endpoint
- The event must have `status` set to `'active'` for the scan to succeed

### Testing Scenarios:

1. **Test successful scan** - Should create/update registration with `attended` status and `external_scanner` type
2. **Test with invalid event_code** - Should return 404
3. **Test with inactive event** - Should return 422
4. **Test with invalid qr_code** - Should return 404
5. **Test with non-member user** - Should return 422
6. **Test duplicate scan** - Should update existing registration (idempotent)
7. **Test missing required fields** - Should return 422 validation error
8. **Test with event code in different case** - Should work (normalized to uppercase)

---

## General Testing Tips

1. **Save Requests in Collection:** Create a Postman collection to organize all API requests
2. **Use Environment Variables:** Set up environment variables for base URL to easily switch between local, staging, and production
3. **Test Error Cases:** Always test with invalid data to ensure proper error handling
4. **Check Response Status Codes:** Verify that the correct HTTP status codes are returned
5. **Validate JSON Structure:** Ensure response JSON matches the expected structure

## Common Issues

1. **CORS Errors:** If testing from a browser, ensure CORS is properly configured
2. **Database State:** Ensure your database has the necessary test data (users, locations, events, etc.)
3. **Server Running:** Make sure your Laravel application is running (`php artisan serve`)

## Quick Test Checklist

- [ ] Server is running
- [ ] Database has test data
- [ ] Request method is correct (GET/POST)
- [ ] URL includes `/api` prefix
- [ ] Headers are set correctly
- [ ] Request body is valid JSON (for POST requests)

---

**Last Updated:** January 2024
**API Version:** 1.0
