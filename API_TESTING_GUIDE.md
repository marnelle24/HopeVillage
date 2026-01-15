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
2. [Get Activity Types](#2-get-activity-types)
3. [Get Locations](#3-get-locations)
4. [Get Events](#4-get-events)
5. [Get Settings](#5-get-settings)

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

## 2. Get Activity Types

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

## 3. Get Locations

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

## 4. Get Events

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

## 5. Get Settings

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
