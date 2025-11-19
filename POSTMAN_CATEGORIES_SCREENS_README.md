# Postman Collection - Categories & Screens CRUD

This Postman collection contains all the endpoints for testing Categories and Screens CRUD operations.

## Files

1. **Auction_Categories_Screens.postman_collection.json** - Postman collection with all API endpoints
2. **Auction_Categories_Screens.postman_environment.json** - Postman environment variables

## How to Import

### Step 1: Import Collection
1. Open Postman
2. Click **Import** button (top left)
3. Select **Auction_Categories_Screens.postman_collection.json**
4. Click **Import**

### Step 2: Import Environment
1. In Postman, click **Import** button
2. Select **Auction_Categories_Screens.postman_environment.json**
3. Click **Import**
4. Select the environment from the dropdown (top right)

## Environment Variables

The environment includes the following variables:

- `base_url` - API base URL (default: `http://localhost:8000`)
- `auth_token` - Automatically set after login (used for admin-protected routes)
- `category_id` - Automatically set after creating/getting a category
- `screen_id` - Automatically set after creating/getting a screen
- `admin_national_id` - Admin national ID for login (default: `1000000001`)
- `admin_password` - Admin password for login (default: `123456`)
- `search_term` - Search term for filtering (optional)
- `category_type` - Category type filter: `terms` or `contracts` (optional)

## Usage Instructions

### 1. Authentication

Before testing admin-protected endpoints, you need to authenticate:

1. Go to **Authentication > Login (Admin)**
2. Update `admin_national_id` and `admin_password` in environment if needed
3. Send the request
4. The auth token will be automatically saved to `auth_token` variable

### 2. Categories Endpoints

#### Public Endpoints (No Authentication Required)

- **Get All Categories** - `GET /api/categories`
  - Optional query params: `search` (search by name), `type` (filter by type)
  - Automatically saves first category ID to `category_id` variable

- **Get Category by ID** - `GET /api/categories/{id}`
  - Uses `{{category_id}}` from environment

#### Admin Endpoints (Requires Authentication)

- **Create Category** - `POST /api/categories`
  - **Required fields:**
    - `name`: string
    - `type`: 'terms' or 'contracts'
    - `image`: image file (jpeg, png, jpg, gif, webp, max 2MB)
  - **Optional fields:**
    - `title`: string
    - `content`: string
    - `screens`: JSON array of objects with `title` and `description`
  - **Note:** Use form-data mode for file upload
  - Automatically saves created category ID to `category_id` variable

- **Update Category** - `PUT /api/categories/{id}`
  - All fields are optional
  - Can update image, name, title, content, type, and screens
  - Uses `{{category_id}}` from environment

- **Delete Category** - `DELETE /api/categories/{id}`
  - Deletes category and all associated screens (cascade delete)
  - Uses `{{category_id}}` from environment

### 3. Screens Endpoints

#### Public Endpoints (No Authentication Required)

- **Get All Screens** - `GET /api/screens`
  - Optional query params: `search` (search by title/description), `category_id` (filter by category)
  - Automatically saves first screen ID to `screen_id` variable

- **Get Screen by ID** - `GET /api/screens/{id}`
  - Uses `{{screen_id}}` from environment

#### Admin Endpoints (Requires Authentication)

- **Create Screen** - `POST /api/screens`
  - **Required fields:**
    - `title`: string
    - `category_id`: integer (must exist in categories table)
  - **Optional fields:**
    - `description`: string
  - Automatically saves created screen ID to `screen_id` variable

- **Update Screen** - `PUT /api/screens/{id}`
  - All fields are optional
  - Uses `{{screen_id}}` from environment

- **Delete Screen** - `DELETE /api/screens/{id}`
  - Uses `{{screen_id}}` from environment

## Example Request Bodies

### Create Category (form-data)

```
name: "Test Category"
title: "Test Category Title"
content: "This is a test category content"
type: "terms"
image: [Select File]
screens: [
    {
        "title": "Screen 1",
        "description": "Description for Screen 1"
    },
    {
        "title": "Screen 2",
        "description": "Description for Screen 2"
    }
]
```

### Create Screen (JSON)

```json
{
    "title": "New Screen Title",
    "description": "This is a description for the new screen",
    "category_id": 1
}
```

## Testing Workflow

1. **Login as Admin** - Get authentication token
2. **Create Category** - Creates a category with image and screens
3. **Get All Categories** - Verify category was created
4. **Get Category by ID** - View specific category details
5. **Update Category** - Modify category details
6. **Create Screen** - Create a new screen for the category
7. **Get All Screens** - Verify screen was created
8. **Update Screen** - Modify screen details
9. **Delete Screen** - Remove a screen
10. **Delete Category** - Remove category (screens will be deleted automatically)

## Notes

- All admin endpoints require Bearer token authentication
- The auth token is automatically saved after login
- Category and Screen IDs are automatically saved after creation/retrieval
- Image uploads must use form-data mode
- Screens array in category creation should be a JSON string in form-data
- All endpoints include automated tests that verify response structure

## Troubleshooting

### 401 Unauthorized
- Make sure you've logged in and the `auth_token` is set
- Check that the token hasn't expired
- Verify you're using an admin account

### 422 Validation Error
- Check required fields are provided
- Verify image file type and size (max 2MB)
- Ensure category_id exists when creating screens
- Check that type is either 'terms' or 'contracts'

### 404 Not Found
- Verify the ID exists in the database
- Check that `category_id` or `screen_id` variables are set correctly

