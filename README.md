# Cardmarket PHP SDK

[![PHPUnit Tests](https://img.shields.io/badge/tests-136%20passing-brightgreen)](https://github.com/pisko999/cardmarket-php-sdk)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-blue)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)

A comprehensive PHP SDK for the [Cardmarket API 2.0](https://api.cardmarket.com/ws/documentation/API_2.0:Main_Page), providing easy access to all marketplace features including stock management, order handling, wantslists, and more.

> **Note:** This SDK is a fork of the original [mamoot64/cardmarket-php-sdk](https://github.com/mamoot64/cardmarket-php-sdk) by Nicolas Perussel, extended with additional features, bug fixes, and comprehensive test coverage.

## Table of Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Quick Start](#quick-start)
- [Authentication](#authentication)
- [API Resources](#api-resources)
  - [Games & Expansions](#games--expansions)
  - [Products & Articles](#products--articles)
  - [Stock Management](#stock-management)
  - [Order Management](#order-management)
  - [Wantslists](#wantslists)
  - [Shopping Cart](#shopping-cart)
  - [Account Management](#account-management)
- [Batch Operations](#batch-operations)
- [Helpers](#helpers)
- [Error Handling](#error-handling)
- [Custom Resources](#custom-resources)
- [Breaking Changes](#breaking-changes)
- [Testing](#testing)
- [API Documentation](#api-documentation)

## Requirements

- PHP 8.1 or higher
- Composer
- Cardmarket API credentials (app token, app secret, access token, access secret)

## Installation

```bash
composer require pisko999/cardmarket-php-sdk
```

## Quick Start

```php
use Pisko\CardMarket\Cardmarket;
use Pisko\CardMarket\HttpClient\HttpClientCreator;

// Initialize the HTTP client with your credentials
$httpCreator = new HttpClientCreator();
$httpCreator->setApplicationToken('your_app_token')
            ->setApplicationSecret('your_app_secret')
            ->setAccessToken('your_access_token')
            ->setAccessSecret('your_access_secret');

// Create the Cardmarket client
$cardmarket = new Cardmarket($httpCreator);

// Start using the API
$games = $cardmarket->games()->getGamesList();
```

## Authentication

The SDK uses OAuth 1.0a authentication. You need four credentials from your Cardmarket developer account:

| Credential | Description |
|------------|-------------|
| `app_token` | Your application's public token |
| `app_secret` | Your application's secret key |
| `access_token` | User-specific access token |
| `access_secret` | User-specific access secret |

### App Types

Cardmarket offers three types of applications:

1. **Dedicated App** - For personal use, managing your own account
2. **Widget App** - Runs within Cardmarket interface (limited API access)
3. **3rd Party App** - For services provided to other users

> **Note:** Some endpoints are restricted based on app type. See the [API documentation](docs/) for details.

### Sandbox vs Production

```php
// Production (default)
$httpCreator = new HttpClientCreator();

// Sandbox environment for testing
$httpCreator = new HttpClientCreator(true); // Pass true for sandbox
```

> **Important:** Sandbox has limited data and some operations may behave differently.

## API Resources

### Games & Expansions

```php
// Get all available TCG games
$games = $cardmarket->games()->getGamesList();

// Get single game details
$game = $cardmarket->games()->getGame(1); // 1 = Magic: The Gathering

// Get all expansions for a game
$expansions = $cardmarket->expansions()->getExpansionsListByGame(1);

// Get expansion details
$expansion = $cardmarket->expansions()->getExpansion(1525);

// Get all cards in an expansion
$cards = $cardmarket->expansions()->getCardsListByExpansion(1525);
```

#### Game IDs

| ID | Game |
|----|------|
| 1 | Magic: The Gathering |
| 2 | World of Warcraft TCG |
| 3 | Yu-Gi-Oh! |
| 5 | The Spoils |
| 6 | Pokémon |
| 7 | Force of Will |
| 8 | Cardfight!! Vanguard |
| 9 | Final Fantasy TCG |
| 10 | Weiß Schwarz |
| 11 | Dragoborne |
| 12 | My Little Pony |
| 13 | Dragon Ball Super |
| 15 | Star Wars: Destiny |

### Products & Articles

```php
// Get product details
$product = $cardmarket->products()->getProductDetails(273799);

// Find products by name
$products = $cardmarket->products()->findProducts('Black Lotus', 0, 100, [
    'idGame' => 1,
    'exact' => false
]);

// Get product list file (CSV)
$productList = $cardmarket->products()->getProductListFile();

// Get price guide
$priceGuide = $cardmarket->prices()->getPriceGuideFile();

// Get articles for a product (other users' offers)
$articles = $cardmarket->articles()->getArticlesByProduct(100569, [
    'minCondition' => 'NM',
    'isFoil' => true,
    'idLanguage' => 1
]);

// Get articles by user
$articles = $cardmarket->articles()->getArticlesByUser('SellerUsername', [
    'idGame' => 1,
    'start' => 0,
    'maxResults' => 100
]);
```

### Stock Management

#### Reading Stock

```php
// Get your stock (paginated, max 100 per request)
$stock = $cardmarket->stock()->getStock(0); // start from 0

// Get single article from stock
$article = $cardmarket->stock()->getStockArticle(142158699);

// Find articles by name
$articles = $cardmarket->stock()->findStockArticles('Pikachu', 6); // 6 = Pokemon

// Get articles for specific product
$articles = $cardmarket->stock()->getStockArticlesOfProduct(100569);

// Get articles currently in other users' carts
$inCarts = $cardmarket->stockInShoppingCarts()->getArticlesListInUsersShoppingCarts();

// Export stock as CSV file (deprecated but still functional)
$stockFile = $cardmarket->stock()->getStockFile(1, false, 1); // gameId, isSealed, languageId
```

#### Adding Articles to Stock

```php
use Pisko\CardMarket\Entities\ArticleEntity;
use Pisko\CardMarket\Entities\ArticlesEntity;

// Create article entity
$article = new ArticleEntity([
    'idProduct' => 100569,
    'idLanguage' => 1,
    'comments' => 'Near Mint condition',
    'count' => 4,
    'price' => 2.50,
    'condition' => 'NM',
    'isFoil' => false,
    'isSigned' => false,
    'isAltered' => false
]);

// Add single article
$response = $cardmarket->addArticleStock()->add([$article->getArray()]);

// Add multiple articles (batch)
$articles = new ArticlesEntity([
    ['idProduct' => 100569, 'count' => 2, 'price' => 2.50, 'condition' => 'NM'],
    ['idProduct' => 100570, 'count' => 1, 'price' => 5.00, 'condition' => 'EX'],
]);
$response = $cardmarket->addArticleStock()->add($articles);
```

#### Updating Articles

```php
// Update article properties (NOT quantity!)
$article = new ArticleEntity([
    'idArticle' => 142158699,
    'idLanguage' => 1,
    'comments' => 'Updated comment',
    'price' => 3.00,
    'condition' => 'NM'
]);

$response = $cardmarket->updateArticleStock()->add([$article->getArray()]);
```

> **Important:** The regular PUT /stock endpoint cannot change quantities! Use `changeQuantity()` instead.

#### Changing Quantities

```php
// Increase or decrease article quantity
$response = $cardmarket->stock()->changeQuantity([
    ['idArticle' => 142158699, 'count' => 2] // Add 2 to existing count
]);
```

#### Deleting Articles

```php
$article = new ArticleEntity([
    'idArticle' => 142158699,
    'count' => 1  // Remove 1 from stock
]);

$response = $cardmarket->deleteArticleStock()->add([$article->getArray()]);
```

#### Stock Export (Async)

```php
// Request stock export
$export = $cardmarket->stockExport()->askStockExport();

// Check export status
$status = $cardmarket->stockExport()->getStockExportStatus();

// Get specific export
$exportDetails = $cardmarket->stockExport()->getStockExport($exportId);
```

### Order Management

```php
// Get received orders (as seller)
$orders = $cardmarket->orders()->getReceivedOrders();

// Get sent orders (as buyer)
$orders = $cardmarket->orders()->getSentOrders();

// Filter orders by state
$orders = $cardmarket->orders()->getOrdersByActorAndState('seller', 'paid');
// States: bought, paid, sent, received, lost, cancelled, evaluated

// Get specific order
$order = $cardmarket->orders()->getOrder(123456);

// Change order state
use Pisko\CardMarket\Entities\OrderChangeStateEntity;

$stateChange = new OrderChangeStateEntity(
    OrderChangeStateEntity::STATE_CHANGE_SEND // Mark as sent
);
$cardmarket->orders()->changeOrderState(123456, $stateChange);

// Add tracking number
use Pisko\CardMarket\Entities\TrackingNumberEntity;

$tracking = new TrackingNumberEntity('1Z999AA10123456784');
$cardmarket->orders()->setTrackingNumber(123456, $tracking);

// Evaluate order
use Pisko\CardMarket\Entities\EvaluationEntity;

$evaluation = new EvaluationEntity(
    EvaluationEntity::GRADE_VERY_GOOD,  // Overall grade
    EvaluationEntity::GRADE_VERY_GOOD,  // Item description accuracy
    EvaluationEntity::GRADE_VERY_GOOD,  // Packaging quality
    'Great seller, fast shipping!',     // Comment
    []                                   // Complaints (empty array)
);
$cardmarket->orders()->evaluateOrder(123456, $evaluation);
```

#### Order States

| State | Description |
|-------|-------------|
| `bought` | Order placed, awaiting payment |
| `paid` | Payment received |
| `sent` | Shipped by seller |
| `received` | Received by buyer |
| `lost` | Package lost |
| `cancelled` | Order cancelled |
| `evaluated` | Order completed and evaluated |

#### Evaluation Grades

| Grade | Constant | Meaning |
|-------|----------|---------|
| 1 | `GRADE_VERY_GOOD` | Very Good |
| 2 | `GRADE_GOOD` | Good |
| 3 | `GRADE_NEUTRAL` | Neutral |
| 4 | `GRADE_BAD` | Bad |
| 10 | `GRADE_NA` | Not Applicable |

### Wantslists

```php
// Get all wantslists
$wantslists = $cardmarket->wantslist()->getWantsLists();

// Get wantslist with items
$wantslist = $cardmarket->wantslist()->getWantsList(211682);

// Create new wantslist
$newList = $cardmarket->wantslist()->createWantsList('EDH Staples', 1); // 1 = MTG

// Rename wantslist
$cardmarket->wantslist()->renameWantsList(211682, 'Updated Name');

// Delete wantslist
$cardmarket->wantslist()->deleteWantsList(211682);
```

#### Managing Wantslist Items

```php
use Pisko\CardMarket\Entities\WantslistItemEntity;
use Pisko\CardMarket\Entities\WantslistItemsEntity;

// Add items
$item = new WantslistItemEntity([
    'idProduct' => 100569,
    'count' => 4,
    'wishPrice' => 10,
    'idLanguage' => 1,
    'minCondition' => 'NM',
    'isFoil' => true
]);
$items = new WantslistItemsEntity([$item]);
$cardmarket->wantslist()->addItemsToWantsList(211682, $items);

// Edit items (requires idWant from existing item)
$item = new WantslistItemEntity([
    'idWant' => 12345,
    'count' => 2,
    'wishPrice' => 15
]);
$items = new WantslistItemsEntity([$item]);
$cardmarket->wantslist()->editItemsInWantsList(211682, $items);

// Delete items
$item = new WantslistItemEntity([
    'idWant' => 12345,
    'count' => 1
]);
$items = new WantslistItemsEntity([$item]);
$cardmarket->wantslist()->deleteItemsFromWantsList(211682, $items);
```

### Shopping Cart

```php
// Get current cart
$cart = $cardmarket->cart()->getCart();

// Add articles to cart
use Pisko\CardMarket\Entities\CartArticlesEntity;

$cartArticles = new CartArticlesEntity([
    ['idArticle' => 123456, 'amount' => 1],
    ['idArticle' => 123457, 'amount' => 2]
]);
$cardmarket->cart()->add($cartArticles);
$cardmarket->cart()->setAction('add');
$cardmarket->cart()->send();

// Remove articles from cart
$cardmarket->cart()->add($cartArticles);
$cardmarket->cart()->setAction('remove');
$cardmarket->cart()->send();

// Empty entire cart
$cardmarket->cart()->emptyCart();

// Checkout
$cardmarket->cart()->checkout();

// Change shipping address
use Pisko\CardMarket\Entities\CartAddressEntity;

$address = new CartAddressEntity(
    'John Doe',           // name
    '',                   // extra (optional)
    '123 Main Street',    // street
    '12345',              // zip
    'Prague',             // city
    'CZ'                  // country code
);
$cardmarket->cart()->changeShippingAddress(12345, $address);

// Change shipping method
use Pisko\CardMarket\Entities\ShippingMethodEntity;

$shipping = new ShippingMethodEntity(2); // shipping method ID
$cardmarket->cart()->changeShippingMethod(12345, $shipping);
```

### Account Management

```php
// Get current account info
$account = $cardmarket->account()->getAccount();

// Get specific user
$user = $cardmarket->users()->getUser('Username');

// Find users
$users = $cardmarket->users()->findUsers('searchterm');

// Messages
$threads = $cardmarket->messages()->getMessageThreads();
$thread = $cardmarket->messages()->getMessageThread(123);

// Send message
use Pisko\CardMarket\Entities\MessageEntity;

$message = new MessageEntity('Hello, I have a question about my order.');
$cardmarket->messages()->sendMessageToUser(123, $message);

// Coupons
$coupons = $cardmarket->coupon()->getCoupons();
$cardmarket->coupon()->redeemCoupon('COUPON-CODE');
```

## Batch Operations

For operations that modify multiple items (add/update/delete stock), the SDK uses automatic batching:

```php
// Async mode - accumulates items, sends when reaching 100
$cardmarket->addArticleStock()->add($articles, true);  // async = true
$cardmarket->addArticleStock()->add($moreArticles, true);

// Force send accumulated items
$response = $cardmarket->addArticleStock()->send();

// Sync mode (default) - sends immediately
$response = $cardmarket->addArticleStock()->add($articles);  // async = false (default)
```

## Helpers

### CsvStockFileHelper

```php
use Pisko\CardMarket\Helpers\CsvStockFileHelper;

$stockFile = $cardmarket->stock()->getStockFile(1);
$helper = new CsvStockFileHelper($stockFile['stock']);

// Save to disk
$helper->storeStockFileOnDisk('./my-stock.csv');
```

### GamesHelper

```php
use Pisko\CardMarket\Helpers\GamesHelper;

// Check if game ID is valid
if (GamesHelper::isGame(1)) {
    echo "Valid game!";
}

// Use constants
$mtgId = GamesHelper::MTG;      // 1
$pokemonId = GamesHelper::PCG;  // 6
```

### UserTypeHelper

```php
use Pisko\CardMarket\Helpers\UserTypeHelper;

// User type constants
UserTypeHelper::PRIVATE_USER;      // 'private'
UserTypeHelper::COMMERCIAL_USER;   // 'commercial'
UserTypeHelper::POWERSELLER;       // 'powerseller'
```

## Error Handling

```php
use Pisko\CardMarket\Exception\HttpClientException;
use Pisko\CardMarket\Exception\HttpServerException;
use Pisko\CardMarket\Exception\HttpClientNotConfiguredException;
use Pisko\CardMarket\Exception\NonExistsResourceException;
use Pisko\CardMarket\Exception\UnknownErrorException;

try {
    $product = $cardmarket->products()->getProductDetails(999999999);
} catch (HttpClientException $e) {
    // 4xx errors (bad request, unauthorized, not found, etc.)
    echo "Client error: " . $e->getMessage();
    echo "Status code: " . $e->getCode();
} catch (HttpServerException $e) {
    // 5xx errors (server errors)
    echo "Server error: " . $e->getMessage();
} catch (HttpClientNotConfiguredException $e) {
    // Missing credentials
    echo "Configuration error: " . $e->getMessage();
}
```

### HTTP Status Codes

| Code | Meaning |
|------|---------|
| 200 | OK - Request successful |
| 206 | Partial Content - Paginated response, more data available |
| 204 | No Content - Request successful, no more data |
| 400 | Bad Request - Invalid parameters |
| 401 | Unauthorized - Invalid credentials |
| 403 | Forbidden - Access denied |
| 404 | Not Found - Resource doesn't exist |
| 500+ | Server Error - Cardmarket server issue |

## Custom Resources

Extend the SDK with your own resources:

```php
namespace App\CardmarketResources;

use Pisko\CardMarket\Resources\HttpCaller;

class MyCustomResource extends HttpCaller
{
    public function getExpensiveCards(int $gameId): array
    {
        $products = $this->get('/products/find?idGame=' . $gameId . '&minPrice=100');
        // Custom logic here
        return $products;
    }
}
```

Register and use:

```php
$cardmarket->registerResources('custom', MyCustomResource::class);
$expensiveCards = $cardmarket->custom()->getExpensiveCards(1);
```

> **Note:** You cannot override default resource names.

## Breaking Changes

### Version 2.0.0 (January 2026)

#### Removed Features

1. **`isPlayset` property removed**
   - As of November 11, 2024, Cardmarket no longer supports playsets
   - Removed from: `ArticleEntity`, `WantslistItemEntity`, `ArticlesResource`
   - **Migration:** Remove any `isPlayset` parameters from your code

2. **Captcha endpoint removed**
   - The `/captcha` endpoint was deprecated in Cardmarket API 2.0
   - Removed: `CaptchaResource` class and `captcha()` method
   - **Migration:** Remove any captcha-related code

#### Method Renames

- `StockExport()` → `stockExport()` (camelCase consistency)

#### Fixed Bugs

- Fixed wrong endpoint in `getStockArticlesOfProduct()` (`/stock/%d` → `/stock/product/%d`)
- Fixed switch case bug in HTTP error handling
- Fixed `hasError()` return type (`int` → `bool`)
- Added XML escaping to all entity XML outputs for security
- Added proper null handling for API response headers

## Testing

```bash
# Clone repository
git clone git@github.com:pisko999/cardmarket-php-sdk.git
cd cardmarket-php-sdk

# Install dependencies
composer install

# Run tests
composer test

# Run static analysis
composer stan

# Check coding standards
composer cs-check

# Fix coding standards
composer cs-fix

# Run all checks (cs-check, stan, test)
composer check

# Run tests with coverage
composer test-coverage-html
```

## API Documentation

Complete API documentation is available in the [docs/](docs/) folder:

- [API Overview](docs/api-overview.md)
- [Authentication](docs/authentication.md)
- [Stock Management](docs/stock-management.md)
- [Order Management](docs/order-management.md)
- [Wantslist Management](docs/wantslist-management.md)
- [Response Codes](docs/response-codes.md)
- [Entities](docs/entities.md)

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Credits

- **Original SDK:** [mamoot64/cardmarket-php-sdk](https://github.com/mamoot64/cardmarket-php-sdk) by [Nicolas Perussel](https://github.com/mamoot64)
- **Current maintainer:** [Petr Spinar](https://github.com/pisko999)
