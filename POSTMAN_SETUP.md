# Postman Collection Setup Guide

## Files Created
1. **Auction_API.postman_collection.json** - Postman collection with all API endpoints
2. **Auction_API.postman_environment.json** - Postman environment variables

## How to Import

### Method 1: Import Collection
1. Open Postman
2. Click **Import** button (top left)
3. Select **Auction_API.postman_collection.json**
4. Click **Import**

### Method 2: Import Environment
1. Open Postman
2. Click **Import** button
3. Select **Auction_API.postman_environment.json**
4. Click **Import**
5. Select the environment from the dropdown (top right)

## Environment Variables

The environment includes:
- `base_url` - API base URL (default: `http://localhost:8000`)
- `auth_token` - Automatically set after login/register (used for protected routes)
- `user_id` - Automatically set after login/register
- `national_id` - Default test national_id

## API Endpoints

### Authentication Endpoints

#### 1. Register
- **Method:** POST
- **URL:** `{{base_url}}/api/register`
- **Body:**
```json
{
    "name": "John Doe",
    "national_id": "1234567890",
    "email": "john.doe@example.com",
    "phone": "+1234567890",
    "address": "123 Main Street, City, Country",
    "summary": "This is a test user account",
    "link": "https://example.com/johndoe",
    "password": "password123",
    "type": "user"
}
```
- **Note:** Token is automatically saved to environment after successful registration

#### 2. Login
- **Method:** POST
- **URL:** `{{base_url}}/api/login`
- **Body:**
```json
{
    "national_id": "1000000001",
    "password": "123456"
}
```
- **Note:** Token is automatically saved to environment after successful login

#### 3. Logout
- **Method:** POST
- **URL:** `{{base_url}}/api/logout`
- **Headers:** Requires `Authorization: Bearer {{auth_token}}`

#### 4. Get Profile
- **Method:** GET
- **URL:** `{{base_url}}/api/profile`
- **Headers:** Requires `Authorization: Bearer {{auth_token}}`

#### 5. Update Profile
- **Method:** PUT
- **URL:** `{{base_url}}/api/profile`
- **Headers:** Requires `Authorization: Bearer {{auth_token}}`
- **Body:**
```json
{
    "name": "John Doe Updated",
    "email": "john.updated@example.com",
    "phone": "+1234567891",
    "address": "456 Updated Street, City, Country",
    "summary": "Updated profile summary",
    "link": "https://example.com/johndoeupdated"
}
```

#### 6. Refresh Token
- **Method:** POST
- **URL:** `{{base_url}}/api/refresh-token`
- **Headers:** Requires `Authorization: Bearer {{auth_token}}`
- **Note:** New token is automatically saved to environment

### Password Reset Endpoints

#### 7. Forget Password
- **Method:** POST
- **URL:** `{{base_url}}/api/forget-password`
- **Body:**
```json
{
    "national_id": "1000000001"
}
```
- **Note:** OTP will be sent to user's email (check your email or logs)

#### 8. Reset Password
- **Method:** POST
- **URL:** `{{base_url}}/api/reset-password`
- **Body:**
```json
{
    "national_id": "1000000001",
    "otp": "123456",
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
}
```

## Testing Workflow

### Recommended Testing Order:

1. **Register** - Create a new user account
   - Token will be automatically saved
   
2. **Login** - Login with existing credentials
   - Use default user: `national_id: 1000000001`, `password: 123456`
   - Token will be automatically saved

3. **Get Profile** - Test protected route
   - Should return user data

4. **Update Profile** - Update user information
   - Should return updated user data

5. **Refresh Token** - Get a new token
   - New token will be automatically saved

6. **Forget Password** - Request password reset OTP
   - Check email for OTP code

7. **Reset Password** - Reset password with OTP
   - Use OTP from email

8. **Logout** - Logout user
   - Token will be invalidated

## Notes

- All protected routes require the `Authorization: Bearer {{auth_token}}` header
- The token is automatically saved after login/register/refresh-token
- Make sure your Laravel server is running on `http://localhost:8000` (or update `base_url` in environment)
- For password reset, ensure email is configured in your `.env` file
- Default test user credentials:
  - National ID: `1000000001`
  - Password: `123456`
  - Email: `nesmaelsaftysm@gmail.com` (from seeder)

## Troubleshooting

1. **401 Unauthorized** - Make sure you've logged in and the token is set in environment
2. **422 Validation Error** - Check request body format and required fields
3. **500 Server Error** - Check Laravel logs in `storage/logs/laravel.log`
4. **OTP not received** - Check email configuration in `.env` and check mail logs

