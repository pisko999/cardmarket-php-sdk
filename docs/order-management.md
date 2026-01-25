# Cardmarket API 2.0 - Order Management

Requests grouped to order management let you retrieve information about your orders, both where you're buyer or seller, as well as change their states (mark them as sent, received, etc.).

## Endpoints Overview

| Resource | Allowed HTTP Methods | Ded | Wid | 3rd | Description |
|----------|---------------------|-----|-----|-----|-------------|
| **Orders** | GET | X | - | X | Returns the Order entity specified by its ID |
| | PUT | X | - | X | Updates the state of the order - it can be marked as sent, as received, can be cancelled, or a cancellation can be requested, resp. the cancellation request can be accepted |
| **Tracking Number** | PUT | X | - | X | Provides a tracking number to an order specified by its ID |
| **Evaluate an Order** | POST | X | - | X | Evaluates an order specified by its ID |
| **Filter Orders** | GET | X | - | X | Returns a collection of orders specified by the actor parameter (buyer or seller) and the state parameter |

## Legend

- **Ded** - Dedicated App
- **Wid** - Widget App
- **3rd** - 3rd Party App
- **X** - Available for this app type
- **-** - Not available for this app type

## Orders

### Get Order

**Endpoint:** `GET /order/{idOrder}`

**Description:** Returns the Order entity specified by its ID.

### Update Order State

**Endpoint:** `PUT /order/{idOrder}`

**Description:** Updates the state of the order - it can be marked as sent, as received, can be cancelled, or a cancellation can be requested, resp. the cancellation request can be accepted.

**Possible State Changes:**
- Mark as sent (seller)
- Mark as received (buyer)
- Cancel order
- Request cancellation
- Accept cancellation request

## Tracking Number

**Endpoint:** `PUT /order/{idOrder}/tracking`

**Description:** Provides a tracking number to an order specified by its ID.

## Evaluate an Order

**Endpoint:** `POST /order/{idOrder}/evaluation`

**Description:** Evaluates an order specified by its ID.

## Filter Orders

**Endpoint:** `GET /orders/{actor}/{state}`

**Description:** Returns a collection of orders specified by the actor parameter (buyer or seller) and the state parameter (bought, paid, sent, received, lost, cancelled). Only orders for the authenticated user are returned.

**Parameters:**
- `actor` - Either `buyer` or `seller`
- `state` - One of: `bought`, `paid`, `sent`, `received`, `lost`, `cancelled`

**Pagination:**
- `start` - Starting index

**Note:** Basically the request returns the complete collection of filtered orders. By specifying the start parameter, the response can be limited to 100 entities.
