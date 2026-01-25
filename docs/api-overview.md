# Cardmarket API 2.0 - Complete Endpoint Overview

Date: January 25, 2026

## API Categories

1. Account Management
2. Market Place Information
3. Order Management
4. Shopping Cart Manipulation
5. Stock Management
6. Wants List Management
7. MKM/API Services

---

## 1. Account Management

| Endpoint | HTTP Methods | Ded | Wid | 3rd | Description |
|----------|-------------|-----|-----|-----|-------------|
| Account Information | GET | ✓ | - | ✓ | Returns the Account entity of the authenticated user |
| Change Vacation Status | PUT | ✓ | - | ✓ | Updates the vacation status |
| Change Display Language | PUT | ✓ | - | ✓ | Updates the display language |
| Messages | GET | ✓ | - | ✓ | Returns message thread overview |
| Messages | POST | ✓ | - | ✓ | Creates a new message |
| Messages | DELETE | ✓ | - | ✓ | Deletes a message thread |
| Find Messages | GET | ✓ | - | ✓ | Finds messages (unread or between dates) |
| Redeem Coupons | POST | ✓ | - | ✓ | Redeems a coupon |
| Request Seller Activation | POST | ✓ | - | ✓ | **DEPRECATED** - Requests seller activation |
| Request Seller Activation | PUT | ✓ | - | ✓ | **DEPRECATED** - Completes seller activation |
| Register New User Account | POST | - | - | ✓ | Registers a new account (3rd party only) |
| Activate New User Account | GET | - | - | ✓ | Requests activation code (3rd party only) |
| Activate New User Account | POST | - | - | ✓ | Activates new account (3rd party only) |
| Request Login Data | POST | - | - | ✓ | Requests login data (3rd party only) |
| Logout | POST | - | - | ✓ | Logs out user, clears cart (3rd party only) |

---

## 2. Market Place Information

| Endpoint | HTTP Methods | Ded | Wid | 3rd | Description |
|----------|-------------|-----|-----|-----|-------------|
| Games | GET | ✓ | ✓ | ✓ | Returns supported games |
| Expansions | GET | ✓ | ✓ | ✓ | Returns expansions for a specific game |
| Expansion Singles | GET | ✓ | ✓ | ✓ | Returns single cards from an expansion |
| Products | GET | ✓ | ✓ | ✓ | Returns Product entity by ID |
| Product List (File) | GET | ✓ | ✓ | ✓ | Returns gzipped CSV file with all products |
| Price Guides (File) | GET | - | ✓ | ✓ | Returns gzipped CSV with price guides (Widget/3rd/Powersellers only) |
| Find Products | GET | ✓ | ✓ | ✓ | Searches products by name |
| Articles | GET | ✓ | ✓ | ✓ | Returns available Article entities by product ID |
| Metaproducts | GET | ✓ | ✓ | ✓ | Returns Metaproduct entity by ID |
| Find Metaproducts | GET | ✓ | ✓ | ✓ | Searches metaproducts by name |
| Users | GET | ✓ | ✓ | ✓ | Returns User entity by ID or name |
| Find Users | GET | ✓ | ✓ | ✓ | Searches users by name |
| User Offers | GET | ✓ | ✓ | ✓ | Returns Article entities from a specific user |
| Export User Offers | GET | ✓ | - | ✓ | Returns user offers export details |
| Export User Offers | POST | ✓ | - | ✓ | Requests user offers export |
| List Requested Exports | GET | ✓ | - | ✓ | Returns all requested user offers exports |

---

## 3. Order Management

| Endpoint | HTTP Methods | Ded | Wid | 3rd | Description |
|----------|-------------|-----|-----|-----|-------------|
| Orders | GET | ✓ | - | ✓ | Returns Order entity by ID |
| Orders | PUT | ✓ | - | ✓ | Updates order state (sent, received, cancelled) |
| Tracking Number | PUT | ✓ | - | ✓ | Adds tracking number to order |
| Evaluate an Order | POST | ✓ | - | ✓ | Evaluates an order |
| Filter Orders | GET | ✓ | - | ✓ | Returns orders collection by filter (buyer/seller, state) |

---

## 4. Shopping Cart Manipulation

| Endpoint | HTTP Methods | Ded | Wid | 3rd | Description |
|----------|-------------|-----|-----|-----|-------------|
| Shopping Cart | GET | ✓ | - | ✓ | Returns Shopping Cart entity |
| Shopping Cart | PUT | ✓ | - | ✓ | Adds or removes articles from cart |
| Shopping Cart | DELETE | ✓ | - | ✓ | Empties the cart |
| Checkout | PUT | ✓ | - | ✓ | Checks out cart and creates orders |
| Shipping Address | PUT | ✓ | - | ✓ | Changes shipping address for cart reservations |
| Shipping Method | GET | ✓ | - | ✓ | Returns available Shipping Method entities |
| Shipping Method | PUT | ✓ | - | ✓ | Changes shipping method for a reservation |

---

## 5. Stock Management

| Endpoint | HTTP Methods | Ded | Wid | 3rd | Description |
|----------|-------------|-----|-----|-----|-------------|
| Stock | GET | ✓ | - | ✓ | Returns Article entities in stock (max 100, requires start) |
| Stock | POST | ✓ | - | ✓ | Adds new articles to stock |
| Stock | PUT | ✓ | - | ✓ | Changes articles in stock (CANNOT change quantity!) |
| Stock | DELETE | ✓ | - | ✓ | Removes articles from stock |
| Stock (File) | GET | ✓ | - | ✓ | **DEPRECATED** - Returns gzipped CSV with stock |
| Stock in Shopping Carts | GET | ✓ | - | ✓ | Returns stock articles in other users' carts |
| Stock Article | GET | ✓ | - | ✓ | Returns single Article by article ID |
| Find Stock Articles | GET | ✓ | - | ✓ | Searches Article entities by name and game |
| Change Stock Article Quantity | PUT | ✓ | - | ✓ | Changes article quantity in stock |
| Stock Articles of Product | GET | ✓ | - | ✓ | Returns Article entities for a specific product |
| Export Your Stock | GET | ✓ | - | ✓ | Returns all requested export details (24h) |
| Export Your Stock | POST | ✓ | - | ✓ | Requests stock article export |
| Stock Export Details | GET | ✓ | - | ✓ | Returns specific export details |

---

## 6. Wants List Management

| Endpoint | HTTP Methods | Ded | Wid | 3rd | Description |
|----------|-------------|-----|-----|-----|-------------|
| Wants Lists | GET | ✓ | - | ✓ | Returns all Wants List entities |
| Wants Lists | POST | ✓ | - | ✓ | Creates a new wants list |
| Wants Lists Items | GET | ✓ | - | ✓ | Returns all Wants List Item entities |
| Wants Lists Items | PUT | ✓ | - | ✓ | Renames wants list OR manages items (add/edit/delete) |
| Wants Lists Items | DELETE | ✓ | - | ✓ | Deletes wants list including all items |

---

## 7. MKM/API Services

| Endpoint | HTTP Methods | Ded | Wid | 3rd | Description |
|----------|-------------|-----|-----|-----|-------------|
| Generate a Captcha | GET | - | - | ✓ | **DEPRECATED** - Generates captcha |

---

## Notes

**Ded** = Dedicated apps  
**Wid** = Widget apps  
**3rd** = 3rd party apps

### Deprecated functions:
- `Request Seller Activation` (POST, PUT)
- `Stock (File)` (GET)
- `Generate a Captcha` (GET)

### Limitations:
- Stock GET requires start parameter (max 100 entities)
- Articles GET limited to 1000 entities, temporary redirect for >100
- User Offers GET requires start and maxResults parameters
- Price Guides (File) GET restricted to Widget/3rd party/Powersellers/Professionals
