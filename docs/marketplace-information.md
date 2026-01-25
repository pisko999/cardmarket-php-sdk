# Cardmarket API 2.0 - Marketplace Information

All Market Place requests give you information about things from MKM's marketplace - games we support, products we list, articles available at the market, or users registered at MKM.

## Endpoints Overview

| Resource | Allowed HTTP Methods | Ded | Wid | 3rd | Description |
|----------|---------------------|-----|-----|-----|-------------|
| **Games** | GET | X | X | X | Returns the Games entities we support at MKM and you can sell and buy cards and products from |
| **Expansions** | GET | X | X | X | Returns Expansion entities for all expansions with single cards for a specific game |
| **Expansion Singles** | GET | X | X | X | Returns Product entities for all single cards in a specified expansion |
| **Products** | GET | X | X | X | Returns the Product entity for the product specified by its ID |
| **Product List (File)** | GET | X | X | X | Returns a gzipped CSV file with all relevant products available at Cardmarket |
| **Price Guides (File)** | GET | - | X | X | Returns a gzipped CSV file with price guides for the specified game. Restricted to Widget apps, 3rd party apps, and Dedicated apps of powersellers and professionals |
| **Find Products** | GET | X | X | X | Searches for a product and returns Product entities of the products found |
| **Articles** | GET | X | X | X | Returns Article entities for available articles specified by their product ID |
| **Metaproducts** | GET | X | X | X | Returns the Metaproduct entity for the metaproduct specified by its ID |
| **Find Metaproducts** | GET | X | X | X | Searches for metaproducts and returns the Metaproduct entity of the metaproducts found |
| **Users** | GET | X | X | X | Returns the User entity for the user specified by its ID or exact name |
| **Find Users** | GET | X | X | X | Returns User entities for the users found |
| **User Offers** | GET | X | X | X | Returns Article entities for available articles from a specific user |
| **Export User Offers** | GET | X | - | X | Returns details for a requested export of Article entities |
| | POST | X | - | X | Request the export of Article entities for available articles from a specific user |
| **List Requested Exports of User Offers** | GET | X | - | X | Returns details for all requested exports of Article entities |

## Legend

- **Ded** - Dedicated App
- **Wid** - Widget App
- **3rd** - 3rd Party App
- **X** - Available for this app type
- **-** - Not available for this app type

## Games

**Endpoint:** `GET /games`

**Description:** Returns the Games entities we support at MKM and you can sell and buy cards and products from.

## Expansions

**Endpoint:** `GET /games/{idGame}/expansions`

**Description:** Returns Expansion entities for all expansions with single cards for a specific game.

## Expansion Singles

**Endpoint:** `GET /expansions/{idExpansion}/singles`

**Description:** Returns Product entities for all single cards in a specified expansion.

## Products

**Endpoint:** `GET /products/{idProduct}`

**Description:** Returns the Product entity for the product specified by its ID.

## Product List (File)

**Endpoint:** `GET /productlist`

**Description:** Returns a gzipped CSV file with all relevant products available at Cardmarket.

## Price Guides (File)

**Endpoint:** `GET /priceguide`

**Availability:** Restricted to Widget apps, 3rd party apps, and Dedicated apps of powersellers and professionals.

**Description:** Returns a gzipped CSV file with price guides for the specified game.

## Find Products

**Endpoint:** `GET /products/find`

**Description:** Searches for a product and returns Product entities of the products found. 

**Search Parameters (query parameters):**
- `search` - A search string for a product's name
- `exact` - A flag, if only products should be returned that exactly match the given search string
- `idGame` - A parameter indicating the game
- `idLanguage` - A parameter indicating the language the search string is provided in

## Articles

**Endpoint:** `GET /articles/{idProduct}`

**Description:** Returns Article entities for available articles specified by their product ID.

**Filter Parameters (query parameters):**
- User state (private, professional, powerseller)
- Minimum user score (outstanding, very good, etc.)
- Language a product is offered in
- Minimum condition (for singles)
- Minimum amount for a single article offered
- Flags indicating foil, signed, and altered singles

**Pagination:**
- `start` - Starting index
- `maxResults` - Number of articles returned

**Note:** Responses with more than 100 entities are temporarily redirected (307) to a request URI specifying a maximum of 100 entities returned. The maximum total count is 1,000 and no entities beyond that maximum is returned if requested.

## Metaproducts

**Endpoint:** `GET /metaproducts/{idMetaproduct}`

**Description:** Returns the Metaproduct entity for the metaproduct specified by its ID.

## Find Metaproducts

**Endpoint:** `GET /metaproducts/find`

**Description:** Searches for metaproducts and returns the Metaproduct entity of the metaproducts found.

**Search Parameters (query parameters):**
- `search` - A search string for the metaproduct's name
- `exact` - A flag indicating if the search string must exactly match the metaproduct's name
- `idGame` - A parameter indicating the game
- `idLanguage` - A parameter indicating the language the search string is provided in

## Users

**Endpoint:** `GET /users/{idUser}`

**Description:** Returns the User entity for the user specified by its ID or exact name.

## Find Users

**Endpoint:** `GET /users/find`

**Description:** Returns User entities for the users found.

**Search Parameters (query parameters):**
- `search` - A search parameter for the user's name (required)

## User Offers

**Endpoint:** `GET /users/{idUser}/articles`

**Description:** Returns Article entities for available articles from a specific user specified by its ID or name.

**Filter Parameters (query parameters):**
- Language a product is offered in
- Minimum condition (for singles)
- Minimum amount for a single article offered
- Flags indicating foil, signed, and altered singles
- `start` - Starting index (mandatory)
- `maxResults` - Maximum number of results (mandatory)

## Export User Offers

**Endpoint:** `GET /users/{idUser}/articles/export/{idExport}`

**Description:** Returns details for a requested export of Article entities for available articles from a specific user specified by its ID.

**Endpoint:** `POST /users/{idUser}/articles/export`

**Description:** Request the export of Article entities for available articles from a specific user specified by its ID.

## List Requested Exports of User Offers

**Endpoint:** `GET /users/{idUser}/articles/export`

**Description:** Returns details for all requested exports of Article entities for available articles from a specific user specified by its ID.
