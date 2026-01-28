/**
 * Simple JWT token decoder using base64
 * @param {string} token - The JWT token
 * @returns {object|null} - Decoded payload or null if invalid
 */
function decodeJWT(token) {
  if (!token) return null
  
  try {
    const parts = token.split('.')
    if (parts.length !== 3) return null
    
    const payload = parts[1]
    const decoded = JSON.parse(atob(payload))
    return decoded
  } catch (error) {
    console.error('Error decoding token:', error)
    return null
  }
}

/**
 * Check if a JWT token is expired
 * @param {string} token - The JWT token to check
 * @returns {boolean} - True if token is expired, false otherwise
 */
export function isTokenExpired(token) {
  if (!token) return true
  
  const decoded = decodeJWT(token)
  if (!decoded || !decoded.exp) return true
  
  const currentTime = Math.floor(Date.now() / 1000)
  return decoded.exp < currentTime
}

/**
 * Get user information from JWT token
 * @param {string} token - The JWT token
 * @returns {object|null} - User information or null if invalid
 */
export function getUserFromToken(token) {
  return decodeJWT(token)
}

/**
 * Check if user has required permissions
 * @param {string} token - The JWT token
 * @param {string} permission - Required permission
 * @returns {boolean} - True if user has permission
 */
export function hasPermission(token, permission) {
  if (!token) return false
  
  try {
    const decoded = decodeJWT(token)
    const scopes = decoded.scopes || []
    return scopes.includes(permission)
  } catch (error) {
    console.error('Error checking permissions:', error)
    return false
  }
}

/**
 * Get token expiration time
 * @param {string} token - The JWT token
 * @returns {Date|null} - Expiration date or null if invalid
 */
export function getTokenExpiration(token) {
  if (!token) return null
  
  const decoded = decodeJWT(token)
  if (!decoded || !decoded.exp) return null
  
  return new Date(decoded.exp * 1000)
} 