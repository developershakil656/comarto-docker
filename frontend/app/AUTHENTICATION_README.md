# Authentication System

This Vue.js application implements a simple authentication system using JWT tokens and Google OAuth.

## How it Works

### 1. Authentication State Management
The authentication state is managed in Vuex store (`src/store/index.js`):

- **State**: Stores user data, token, and authentication status
- **Getters**: Provide access to authentication state
- **Mutations**: Update authentication state
- **Actions**: Handle authentication operations

### 2. Login Flow
1. User clicks "Login with Google" button
2. Redirects to Google OAuth
3. Google redirects back with authorization code
4. Backend exchanges code for user data and JWT token
5. Store saves user data and token
6. Token is automatically added to all API requests

### 3. Token Management
- Tokens are stored in localStorage
- Automatically added to axios headers for API requests
- Backend validates tokens on protected routes
- Invalid tokens are cleared automatically

### 4. Route Protection
Protected routes use meta fields and component-level authentication checks:
- `/user/*` - User profile routes
- `/my/business/*` - Business dashboard routes

Components can use the `authMixin` to automatically check authentication on mount.

## Usage in Components

### Check if user is authenticated:
```javascript
// Option 1: Use the authMixin
import authMixin from '@/mixins/authMixin.js'

export default {
  mixins: [authMixin],
  // Now you have access to isAuthenticated and user computed properties
}

// Option 2: Manual implementation
computed: {
  isAuthenticated() {
    return this.$store.getters.isAuthenticated;
  },
  user() {
    return this.$store.getters.user;
  }
}
```

### Login:
```javascript
methods: {
  async login() {
    await this.$store.dispatch('googleLogin');
  }
}
```

### Logout:
```javascript
methods: {
  logout() {
    this.$store.dispatch('logout');
  }
}
```

## API Endpoints

- `POST /user/google/login` - Exchange Google code for token
- `GET /user/me` - Get current user data (protected)

## File Structure

- `src/store/index.js` - Authentication state management
- `src/utils/auth.js` - Token utility functions
- `src/mixins/authMixin.js` - Authentication mixin for components
- `src/views/GoogleCallbackView.vue` - Handle Google OAuth callback
- `src/components/modals/LoginModal.vue` - Login modal
- `src/components/header/TopHeader.vue` - Header with auth UI
- `src/components/user/UserSlider.vue` - User profile sidebar

## Security Features

- JWT tokens with expiration
- Automatic token validation on app startup
- Protected routes with automatic redirects
- Secure token storage in localStorage
- Automatic cleanup of invalid tokens 