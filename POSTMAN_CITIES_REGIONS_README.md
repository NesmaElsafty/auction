# Postman Collection - Cities & Regions CRUD

This Postman collection contains all the endpoints for testing Cities and Regions CRUD operations.

## Files

1. **Auction_Cities_Regions.postman_collection.json** - Postman collection with all API endpoints
2. **Auction_Cities_Regions.postman_environment.json** - Postman environment variables

## How to Import

### Step 1: Import Collection
1. Open Postman
2. Click **Import** button (top left)
3. Select **Auction_Cities_Regions.postman_collection.json**
4. Click **Import**

### Step 2: Import Environment
1. In Postman, click **Import** button
2. Select **Auction_Cities_Regions.postman_environment.json**
3. Click **Import**
4. Select the environment from the dropdown (top right)

## Environment Variables

The environment includes the following variables:

- `base_url` - API base URL (default: `http://localhost:8000`)
- `auth_token` - Automatically set after login (used for admin-protected routes)
- `city_id` - Automatically set after creating/getting a city
- `region_id` - Automatically set after creating/getting a region
- `admin_national_id` - Admin national ID for login (default: `1000000001`)
- `admin_password` - Admin password for login (default: `123456`)
- `search_term` - Search term for filtering (optional)

## Usage Instructions

### 1. Authentication

Before testing admin-protected endpoints, you need to authenticate:

1. Go to **Authentication > Login (Admin)**
2. Update `admin_national_id` and `admin_password` in environment if needed
3. Send the request
4. The auth token will be automatically saved to `auth_token` variable

### 2. Cities Endpoints

#### Public Endpoints (No Authentication Required)

- **Get All Cities** - `GET /api/cities`
  - Optional query parameters:
    - `search` - Search by city name
  - Example: `GET /api/cities?search=الرياض`

- **Get City by ID** - `GET /api/cities/{id}`
  - Returns city with all its regions
  - Example: `GET /api/cities/1`

#### Admin Endpoints (Requires Authentication)

- **Create City** - `POST /api/cities`
  - Required fields:
    - `name` - string (city name)
  - Example body:
    ```json
    {
        "name": "الرياض"
    }
    ```

- **Update City** - `PUT /api/cities/{id}`
  - All fields are optional
  - Example body:
    ```json
    {
        "name": "الرياض المحدثة"
    }
    ```

- **Delete City** - `DELETE /api/cities/{id}`
  - This will also delete all associated regions (cascade delete)

### 3. Regions Endpoints

#### Public Endpoints (No Authentication Required)

- **Get All Regions** - `GET /api/regions`
  - Optional query parameters:
    - `search` - Search by region name
    - `city_id` - Filter by city ID
  - Example: `GET /api/regions?search=العليا&city_id=1`

- **Get Region by ID** - `GET /api/regions/{id}`
  - Returns region with its city information
  - Example: `GET /api/regions/1`

#### Admin Endpoints (Requires Authentication)

- **Create Region** - `POST /api/regions`
  - Required fields:
    - `name` - string (region name)
    - `city_id` - integer (must exist in cities table)
  - Example body:
    ```json
    {
        "name": "العليا",
        "city_id": 1
    }
    ```

- **Update Region** - `PUT /api/regions/{id}`
  - All fields are optional
  - Example body:
    ```json
    {
        "name": "العليا المحدثة",
        "city_id": 1
    }
    ```

- **Delete Region** - `DELETE /api/regions/{id}`

## Testing Workflow

### Recommended Testing Order:

1. **Login (Admin)** - Authenticate to get admin token
   - Token will be automatically saved to `auth_token`

2. **Get All Cities (Public)** - List all cities
   - First city ID will be automatically saved to `city_id`

3. **Get City by ID (Public)** - Get specific city with regions
   - City ID will be saved to `city_id`

4. **Create City (Admin)** - Create a new city
   - City ID will be automatically saved to `city_id`

5. **Update City (Admin)** - Update the created city
   - Use the saved `city_id`

6. **Create Region (Admin)** - Create a region for the city
   - Region ID will be automatically saved to `region_id`
   - Make sure `city_id` is set before creating a region

7. **Get All Regions (Public)** - List all regions
   - First region ID will be automatically saved to `region_id`
   - You can filter by `city_id` using query parameter

8. **Get Region by ID (Public)** - Get specific region with city
   - Region ID will be saved to `region_id`

9. **Update Region (Admin)** - Update the created region
   - Use the saved `region_id`

10. **Delete Region (Admin)** - Delete the region
    - Use the saved `region_id`

11. **Delete City (Admin)** - Delete the city
    - This will cascade delete all associated regions
    - Use the saved `city_id`

## Test Scripts

Each endpoint includes automated test scripts that:
- Verify status codes (200, 201, 404, 500)
- Check response structure (`success`, `data`, `message`)
- Validate required fields in responses
- Automatically save IDs to environment variables for chained requests

## Notes

- All admin endpoints require Bearer token authentication
- City deletion will cascade delete all associated regions
- Search functionality is case-insensitive
- Pagination is supported (default: 10 items per page)
- All timestamps are included in responses (`created_at`, `updated_at`)

## Example Responses

### City Response
```json
{
    "success": true,
    "message": "City retrieved successfully",
    "data": {
        "id": 1,
        "name": "الرياض",
        "regions": [
            {
                "id": 1,
                "name": "العليا",
                "city_id": 1,
                "created_at": "2024-01-01T00:00:00.000000Z",
                "updated_at": "2024-01-01T00:00:00.000000Z"
            }
        ],
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T00:00:00.000000Z"
    }
}
```

### Region Response
```json
{
    "success": true,
    "message": "Region retrieved successfully",
    "data": {
        "id": 1,
        "name": "العليا",
        "city_id": 1,
        "city": {
            "id": 1,
            "name": "الرياض",
            "created_at": "2024-01-01T00:00:00.000000Z",
            "updated_at": "2024-01-01T00:00:00.000000Z"
        },
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T00:00:00.000000Z"
    }
}
```

