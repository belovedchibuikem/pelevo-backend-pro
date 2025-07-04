# API Documentation

## Base URL
All API endpoints are prefixed with `/api`

## Authentication
Most endpoints require authentication using Laravel Sanctum. Include the token in the Authorization header:
```
Authorization: Bearer <your-token>
```

## Authentication Endpoints

### Register
- **URL**: `/api/register`
- **Method**: `POST`
- **Auth Required**: No
- **Description**: Register a new user

### Login
- **URL**: `/api/login`
- **Method**: `POST`
- **Auth Required**: No
- **Description**: Login and get authentication token

### Google Authentication
- **URL**: `/api/auth/google/redirect`
- **Method**: `POST`
- **Auth Required**: No
- **Description**: Redirect to Google OAuth

- **URL**: `/api/auth/google/callback`
- **Method**: `GET`
- **Auth Required**: No
- **Description**: Handle Google OAuth callback

### App Sign In
- **URL**: `/api/auth/app-signin`
- **Method**: `POST`
- **Auth Required**: No
- **Description**: Sign in from mobile app

### Logout
- **URL**: `/api/logout`
- **Method**: `POST`
- **Auth Required**: Yes
- **Description**: Logout and invalidate token

### Get User
- **URL**: `/api/user`
- **Method**: `GET`
- **Auth Required**: Yes
- **Description**: Get authenticated user details

## Podcast Endpoints

### Categories
- **URL**: `/api/podcasts/categories`
- **Method**: `GET`
- **Auth Required**: Yes
- **Description**: Get all podcast categories

### Search Podcasts
- **URL**: `/api/podcasts/search`
- **Method**: `GET`
- **Auth Required**: Yes
- **Description**: Search for podcasts

### Featured Podcasts
- **URL**: `/api/podcasts/featured`
- **Method**: `GET`
- **Auth Required**: Yes
- **Description**: Get featured podcasts

### Recommended Podcasts
- **URL**: `/api/podcasts/recommended`
- **Method**: `GET`
- **Auth Required**: Yes
- **Description**: Get recommended podcasts

### New Episodes
- **URL**: `/api/podcasts/new-episodes`
- **Method**: `GET`
- **Auth Required**: Yes
- **Description**: Get new episodes

### User Subscriptions
- **URL**: `/api/podcasts/subscriptions`
- **Method**: `GET`
- **Auth Required**: Yes
- **Description**: Get user's podcast subscriptions

### Subscribe to Podcast
- **URL**: `/api/podcasts/subscribe`
- **Method**: `POST`
- **Auth Required**: Yes
- **Description**: Subscribe to a podcast

### Unsubscribe from Podcast
- **URL**: `/api/podcasts/unsubscribe`
- **Method**: `DELETE`
- **Auth Required**: Yes
- **Description**: Unsubscribe from a podcast

### Toggle Notifications
- **URL**: `/api/podcasts/notifications/toggle`
- **Method**: `POST`
- **Auth Required**: Yes
- **Description**: Toggle podcast notifications

### Get Podcast Details
- **URL**: `/api/podcasts/{showId}`
- **Method**: `GET`
- **Auth Required**: Yes
- **Description**: Get detailed information about a specific podcast

### Get Episode Details
- **URL**: `/api/podcasts/episodes/{episodeId}`
- **Method**: `GET`
- **Auth Required**: Yes
- **Description**: Get detailed information about a specific episode

## Earnings Endpoints

### Record Listening
- **URL**: `/api/episodes/{episode}/listen`
- **Method**: `POST`
- **Auth Required**: Yes
- **Description**: Record a listening session for an episode

### Get Total Earnings
- **URL**: `/api/earnings/total`
- **Method**: `GET`
- **Auth Required**: Yes
- **Description**: Get total earnings

### Get Earnings by Date
- **URL**: `/api/earnings/by-date`
- **Method**: `GET`
- **Auth Required**: Yes
- **Description**: Get earnings grouped by date

### Get Earnings by Podcast
- **URL**: `/api/earnings/by-podcast/{podcastId}`
- **Method**: `GET`
- **Auth Required**: Yes
- **Description**: Get earnings for a specific podcast

### Get Listening History
- **URL**: `/api/listening-history`
- **Method**: `GET`
- **Auth Required**: Yes
- **Description**: Get user's listening history

## Withdrawal Endpoints

### Request Withdrawal
- **URL**: `/api/withdraw`
- **Method**: `POST`
- **Auth Required**: Yes
- **Description**: Request a withdrawal

### Get Withdrawals
- **URL**: `/api/withdrawals`
- **Method**: `GET`
- **Auth Required**: Yes
- **Description**: Get user's withdrawal history

### Get Withdrawal Status
- **URL**: `/api/withdrawals/{withdrawal}/status`
- **Method**: `GET`
- **Auth Required**: Yes
- **Description**: Get status of a specific withdrawal

## Notification Endpoints

### Get Notifications
- **URL**: `/api/notifications`
- **Method**: `GET`
- **Auth Required**: Yes
- **Description**: Get user's notifications

### Get Unread Notifications
- **URL**: `/api/notifications/unread`
- **Method**: `GET`
- **Auth Required**: Yes
- **Description**: Get user's unread notifications

### Mark Notification as Read
- **URL**: `/api/notifications/{notification}/read`
- **Method**: `POST`
- **Auth Required**: Yes
- **Description**: Mark a notification as read

### Mark All Notifications as Read
- **URL**: `/api/notifications/read-all`
- **Method**: `POST`
- **Auth Required**: Yes
- **Description**: Mark all notifications as read

## Admin Dashboard Endpoints

### Get Dashboard Data
- **URL**: `/api/admin/dashboard`
- **Method**: `GET`
- **Auth Required**: Yes
- **Description**: Get admin dashboard data

### Get Payouts
- **URL**: `/api/admin/payouts`
- **Method**: `GET`
- **Auth Required**: Yes
- **Description**: Get payout information

### Get Blocked IPs
- **URL**: `/api/admin/blocked-ips`
- **Method**: `GET`
- **Auth Required**: Yes
- **Description**: Get list of blocked IP addresses

### Get Users by Country
- **URL**: `/api/admin/users-by-country`
- **Method**: `GET`
- **Auth Required**: Yes
- **Description**: Get user distribution by country

## Taddy API Endpoints (Flutter App)

### Search Podcasts
- **URL**: `/api/taddy/search`
- **Method**: `GET`
- **Auth Required**: Yes
- **Description**: Search for podcasts (Flutter app specific)

### Get Podcast Details with Episodes
- **URL**: `/api/taddy/podcasts/{taddyId}`
- **Method**: `GET`
- **Auth Required**: Yes
- **Description**: Get podcast details including episodes (Flutter app specific)

### Get Categories
- **URL**: `/api/taddy/categories`
- **Method**: `GET`
- **Auth Required**: Yes
- **Description**: Get podcast categories (Flutter app specific)

### Get Featured Podcasts
- **URL**: `/api/taddy/featured-podcasts`
- **Method**: `GET`
- **Auth Required**: Yes
- **Description**: Get featured podcasts (Flutter app specific)

### Get New Episodes
- **URL**: `/api/taddy/new-episodes`
- **Method**: `GET`
- **Auth Required**: Yes
- **Description**: Get new episodes (Flutter app specific)

### Get Recommended Podcasts
- **URL**: `/api/taddy/recommended-podcasts`
- **Method**: `GET`
- **Auth Required**: Yes
- **Description**: Get recommended podcasts (Flutter app specific)

### Get Subscribed Podcasts
- **URL**: `/api/taddy/subscribed-podcasts`
- **Method**: `GET`
- **Auth Required**: Yes
- **Description**: Get user's subscribed podcasts (Flutter app specific) 