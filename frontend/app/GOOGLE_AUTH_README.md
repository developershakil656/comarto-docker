# Google Authentication Implementation

This document describes the Google OAuth authentication implementation for the Vue.js frontend application.

## Overview

The application now supports Google OAuth authentication using Laravel Socialite on the backend. Users can log in using their Google accounts, and the authentication state is managed through Vuex.

## Implementation Details

### 1. Vuex Store Authentication State

The authentication state is managed in `src/store/index.js`:

- **State**: `user`, `isAuthenticated`, `authLoading`
- **Getters**: `user`, `isAuthenticated`, `authLoading`
- **Mutations**: `SET_USER`, `SET_AUTH_LOADING`
- **Actions**: `googleLogin`, `handleGoogleCallback`, `logout`

### 2. Components Updated

#### LoginModal.vue
- Added Google login button that handles Google authentication directly
- Uses existing Google icon from assets

#### TopHeader.vue
- Updated to show user profile when authenticated
- Added computed properties for authentication state

#### UserSlider.vue
- Updated to display authenticated user information
- Added logout functionality
- Shows user name, email, and profile image

### 3. Routes

#### Google Callback Route
- **Path**: `/user/auth/google/callback`
- **Component**: `GoogleCallbackView.vue`
- **Purpose**: Handles the OAuth callback from Google

### 4. Authentication Flow

1. **User clicks "Login with Google"** in LoginModal
2. **Frontend redirects** to `/user/auth/google/redirect` (Laravel backend)
3. **Laravel redirects** to Google OAuth consent screen
4. **User authorizes** the application
5. **Google redirects** back to `/user/auth/google/callback` with authorization code
6. **Frontend processes** the callback and exchanges code for user data
7. **User data is stored** in Vuex and localStorage
8. **User is redirected** to home page with success message

### 5. Backend API Endpoints

The implementation expects these Laravel endpoints:

- `GET /user/auth/google/redirect` - Initiates Google OAuth flow
- `GET /user/auth/google/callback` - Handles OAuth callback and returns user data

### 6. User Data Structure

The expected user data structure from the backend:

```javascript
{
  id: number,
  name: string,
  email: string,
  profileImage: string, // Google profile picture URL
  // ... other user fields
}
```

### 7. Error Handling

The implementation includes error handling for:
- OAuth authorization failures
- Missing authorization codes
- Callback processing failures
- Network errors

### 8. Security Considerations

- User data is stored in localStorage (consider using httpOnly cookies for production)
- Authentication state is managed client-side
- OAuth flow uses standard Google OAuth 2.0 protocol

## Usage

### For Users
1. Click "Login / Sign Up" button in the header
2. Click "Login with Google" button in the modal
3. Authorize the application on Google's consent screen
4. User is automatically logged in and redirected to home page

### For Developers
1. Ensure Laravel backend has Google OAuth configured
2. Set up proper redirect URIs in Google Console
3. Configure environment variables for Google OAuth credentials
4. Test the authentication flow end-to-end

## Environment Variables

The backend should have these environment variables configured:

```env
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT_URI=http://your-domain/user/auth/google/callback
```

## Future Enhancements

- Add refresh token handling
- Implement token expiration management
- Add social login options (Facebook, Twitter, etc.)
- Implement proper session management
- Add user profile editing capabilities 