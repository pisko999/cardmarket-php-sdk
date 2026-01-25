# Cardmarket API 2.0 - Stock Management

Requests grouped to stock management let you retrieve information about the articles you want to sell, as well as put new articles into the stock, remove them from, or edit them.

## Endpoints Overview

| Resource | Allowed HTTP Methods | Ded | Wid | 3rd | Description |
|----------|---------------------|-----|-----|-----|-------------|
| **Stock** | GET | X | - | X | Returns the Article entities in the authenticated user's stock. Due to performance reasons, a start parameter must be specified and the response is limited to 100 entities |
| | POST | X | - | X | Adds new articles to the user's stock |
| | PUT | X | - | X | Changes articles in the user's stock. Attention: This request can't be used to increase or decrease the stock's quantity for specified articles! |
| | DELETE | X | - | X | Removes articles from the user's stock |
| **Stock (File)** (DEPRECATED) | GET | X | - | X | Returns a gzipped CSV file with all articles in the authenticated user's stock, further specified by a game and language |
| **Stock in Shopping Carts** | GET | X | - | X | Returns the Article entities of the authenticated user's stock that are currently in other user's shopping carts |
| **Stock Article** | GET | X | - | X | Returns a single Article entity in the authenticated user's stock specified by its article ID |
| **Find Stock Articles** | GET | X | - | X | Searches for and returns Article entities specified by the article's name and game |
| **Change Stock Article Quantity** | PUT | X | - | X | Changes quantities for articles in authenticated user's stock |
| **Stock Articles of Product** | GET | X | - | X | Returns the Article entities of the authenticated user's stock that belong to the specified product |
| **Export Your Stock and List All Requests** | GET | X | - | X | Returns details for all requested exports of Article entities from your stock from the last 24 hours |
| | POST | X | - | X | Request the export of Article entities from your stock |
| **Stock Export Details** | GET | X | - | X | Returns details for a requested export of Article entities from your stock |

## Legend

- **Ded** - Dedicated App
- **Wid** - Widget App
- **3rd** - 3rd Party App
- **X** - Available for this app type
- **-** - Not available for this app type

## Stock

### Get Stock

**Endpoint:** `GET /stock`

**Description:** Returns the Article entities in the authenticated user's stock. Returns the stock. Due to performance reasons, a start parameter must be specified and the response is limited to 100 entities.

**Parameters:**
- `start` - Starting index (required)

### Add Articles to Stock

**Endpoint:** `POST /stock`

**Description:** Adds new articles to the user's stock.

### Update Articles in Stock

**Endpoint:** `PUT /stock`

**Description:** Changes articles in the user's stock.

**Attention:** This request can't be used to increase or decrease the stock's quantity for specified articles! Use the Change Stock Article Quantity endpoint instead.

### Remove Articles from Stock

**Endpoint:** `DELETE /stock`

**Description:** Removes articles from the user's stock.

## Stock (File) - DEPRECATED

**Endpoint:** `GET /stock/file`

**Description:** Returns a gzipped CSV file with all articles in the authenticated user's stock, further specified by a game and language.

**Note:** This endpoint is deprecated.

## Stock in Shopping Carts

**Endpoint:** `GET /stock/shoppingcart-articles`

**Description:** Returns the Article entities of the authenticated user's stock that are currently in other user's shopping carts.

## Stock Article

**Endpoint:** `GET /stock/article/{idArticle}`

**Description:** Returns a single Article entity in the authenticated user's stock specified by its article ID.

## Find Stock Articles

**Endpoint:** `GET /stock/articles/{searchString}/{idGame}`

**Description:** Searches for and returns Article entities specified by the article's name and game.

## Change Stock Article Quantity

**Endpoint:** `PUT /stock/quantity`

**Description:** Changes quantities for articles in authenticated user's stock.

**Note:** This is the correct endpoint to use when you need to increase or decrease article quantities. The regular PUT /stock endpoint cannot be used for this purpose.

## Stock Articles of Product

**Endpoint:** `GET /stock/product/{idProduct}`

**Description:** Returns the Article entities of the authenticated user's stock that belong to the specified product.

## Export Your Stock and List All Requests

### List Export Requests

**Endpoint:** `GET /stock/exports`

**Description:** Returns details for all requested exports of Article entities from your stock from the last 24 hours.

### Request Stock Export

**Endpoint:** `POST /stock/exports`

**Description:** Request the export of Article entities from your stock.

## Stock Export Details

**Endpoint:** `GET /stock/exports/{idExport}`

**Description:** Returns details for a requested export of Article entities from your stock.
