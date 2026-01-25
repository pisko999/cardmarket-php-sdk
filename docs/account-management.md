# Cardmarket API 2.0 - Account Management

All Account requests allow managing the authenticated user's account or are in some way connected to account management.

## Endpoints Overview

| Resource | Allowed HTTP Methods | Ded | Wid | 3rd | Description |
|----------|---------------------|-----|-----|-----|-------------|
| **Account Information** | GET | X | - | X | Returns the Account entity of the authenticated user |
| **Change Vacation Status** | PUT | X | - | X | Updates the vacation status of the authenticated user; returns the Account entity of the authenticated user |
| **Change Display Language** | PUT | X | - | X | Updates the display language of the authenticated user; returns the Account entity of the authenticated user |
| **Messages** | GET | X | - | X | Returns the message thread overview (resp. a message thread with a specified other user) of the authenticated user |
| | POST | X | - | X | Creates a new message for the authenticated user to a specified other user |
| | DELETE | X | - | X | Deletes a message thread for the authenticated user to a specified other user (resp. a specified single message) |
| **Find Messages** | GET | X | - | X | Finds messages - either all unread or between two dates |
| **Redeem Coupons** | POST | X | - | X | Redeems a coupon to the authenticated user's account |
| **Request Seller Activation** (DEPRECATED) | POST | X | - | X | Requests the seller activation of the authenticated user account |
| | PUT | X | - | X | Completes the requested seller activation for the authenticated user |
| **Register New User Account** | POST | - | - | X | Registers a new private user account (only available to 3rd Party apps) |
| **Activate New User Account** | GET | - | - | X | Requests the activation code for a specified and authenticated user account (only available to 3rd Party apps) |
| | POST | - | - | X | Activates a newly registered user account (only available to 3rd Party apps) |
| **Request Login Data** | POST | - | - | X | Requests the login data (username and/or password) for a user account (only available to 3rd Party apps) |
| **Logout** | POST | - | - | X | Logs out the current authenticated user, clears his shopping cart and invalidates his access tokens |

## Legend

- **Ded** - Dedicated App
- **Wid** - Widget App
- **3rd** - 3rd Party App
- **X** - Available for this app type
- **-** - Not available for this app type

## Account Information

**Endpoint:** `GET /account`

**Description:** Returns the Account entity of the authenticated user.

## Change Vacation Status

**Endpoint:** `PUT /account/vacation`

**Description:** Updates the vacation status of the authenticated user; returns the Account entity of the authenticated user.

## Change Display Language

**Endpoint:** `PUT /account/language`

**Description:** Updates the display language of the authenticated user; returns the Account entity of the authenticated user.

## Messages

**Endpoint:** `GET /account/messages`

**Description:** Returns the message thread overview of the authenticated user.

**Endpoint:** `GET /account/messages/{idUser}`

**Description:** Returns a message thread with a specified other user.

**Endpoint:** `POST /account/messages/{idUser}`

**Description:** Creates a new message for the authenticated user to a specified other user.

**Endpoint:** `DELETE /account/messages/{idUser}`

**Description:** Deletes a message thread for the authenticated user to a specified other user.

**Endpoint:** `DELETE /account/messages/{idUser}/{idMessage}`

**Description:** Deletes a specified single message.

## Find Messages

**Endpoint:** `GET /account/messages/find`

**Description:** Finds messages - either all unread or between two dates.

## Redeem Coupons

**Endpoint:** `POST /account/coupons`

**Description:** Redeems a coupon to the authenticated user's account.

## Request Seller Activation (DEPRECATED)

**Endpoint:** `POST /account/selleractivation`

**Description:** Requests the seller activation of the authenticated user account.

**Endpoint:** `PUT /account/selleractivation`

**Description:** Completes the requested seller activation for the authenticated user.

## Register New User Account

**Endpoint:** `POST /register`

**Availability:** Only available to 3rd Party apps.

**Description:** Registers a new private user account.

## Activate New User Account

**Endpoint:** `GET /activate/{username}`

**Availability:** Only available to 3rd Party apps.

**Description:** Requests the activation code for a specified and authenticated user account.

**Endpoint:** `POST /activate/{username}`

**Availability:** Only available to 3rd Party apps.

**Description:** Activates a newly registered user account.

## Request Login Data

**Endpoint:** `POST /requestlogindata`

**Availability:** Only available to 3rd Party apps.

**Description:** Requests the login data (username and/or password) for a user account.

## Logout

**Endpoint:** `POST /logout`

**Availability:** Only available to 3rd Party apps.

**Description:** Logs out the current authenticated user, clears his shopping cart and invalidates his access tokens.
