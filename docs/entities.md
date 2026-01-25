# Cardmarket API 2.0 - Entities

## Main Entities

Entities represent data structures returned from the API. Below is a complete list of all entities available in API 2.0.

### Account
Entity representing a user account with information about settings, balance, reputation, etc.

### User
Entity representing a Cardmarket user with information about sales, ratings, location, etc.

### MessageThread
Entity representing a message thread between users.

### Game
Entity representing a game supported on Cardmarket (Magic the Gathering, Pokemon, Yu-Gi-Oh!, etc.).

### Expansion
Entity representing an expansion/set of a specific game.

### Product
Entity representing a specific product (card, sealed product, etc.) with information about price, availability, rarity, etc.

### Metaproduct
Entity representing a metaproduct - a group of products with the same name across different expansions.

### Article
Entity representing a specific product offer from a seller with price, condition, language, etc.

### Order
Entity representing an order between buyer and seller with information about state, total price, shipping method, etc.

### ShoppingCart
Entity representing a shopping cart with reservations from different sellers.

### WantsList
Entity representing a wantslist (list of wanted cards) of a user.

### WantsListItem
Entity representing an individual item in a wantslist with information about the requested product, price, conditions, etc.

### UserOffersExport
Entity representing an export of a specific user's offers.

### StockExport
Entity representing an export of a user's stock.

### CreateArticleResponse
Entity representing the response when creating new articles in stock.

### UpdateArticleResponse
Entity representing the response when updating articles in stock.

## Help Entities

Help entities are used as parts of main entities.

### Address
Entity representing an address with street, city, postal code, country, etc.

### Evaluation
Entity representing an order evaluation with comment and rating.

### ShippingMethod
Entity representing a shipping method with name, price, and delivery time.

### Localization
Entity representing localization information (languages, currencies, etc.).

### Captcha
Entity representing captcha for verification (deprecated).

## HATEOAS Links

API 2.0 implements HATEOAS (Hypermedia as the Engine of Application State) principles. Each entity returned from the API contains links to related resources.

Example of HATEOAS links in response:
```json
{
  "product": {
    "idProduct": 100569,
    "name": "Black Lotus",
    "links": [
      {
        "rel": "self",
        "href": "/products/100569",
        "method": "GET"
      },
      {
        "rel": "articles",
        "href": "/articles/100569",
        "method": "GET"
      }
    ]
  }
}
```

These links allow API navigation without hardcoding URLs and enable API functionality discovery.

## Version Notes

Entities in API 2.0 are not compatible with version 1.1 (and 1.0). Before transitioning to version 2.0, ensure your application and response entity processing has been updated.
