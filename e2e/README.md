# E2E Tests for Cardmarket PHP SDK

End-to-end tests against live Cardmarket API.

## ⚠️ Warning

These tests run against the **LIVE** Cardmarket API and may:
- Create/modify/delete items in your stock
- Create/modify/delete wantslists
- Send messages
- Affect your account settings

**Use a dedicated test account with limited stock/funds!**

## Setup

1. Copy the example environment file:
   ```bash
   cp e2e/.env.example e2e/.env
   ```

2. Edit `e2e/.env` with your Cardmarket API credentials:
   ```env
   CARDMARKET_APP_TOKEN=your_app_token
   CARDMARKET_APP_SECRET=your_app_secret
   CARDMARKET_ACCESS_TOKEN=your_access_token
   CARDMARKET_ACCESS_SECRET=your_access_secret
   ```

3. Get your API credentials at: https://www.cardmarket.com/en/Magic/Account/API

## Usage

### Run all tests
```bash
composer e2e
```

### Run specific test suite
```bash
composer e2e:games      # Games & Expansions (read-only)
composer e2e:products   # Products & Articles (read-only)
composer e2e:stock      # Stock Management (modifies stock!)
composer e2e:orders     # Orders (read-only)
composer e2e:wantslists # Wantslists (creates/deletes wantslists!)
composer e2e:account    # Account & Messages
```

### Run with debug output
```bash
E2E_DEBUG=true composer e2e
```

### List available tests
```bash
php e2e/run-tests.php --list
```

## Test Suites

| Suite | Tests | Read-Only | Description |
|-------|-------|-----------|-------------|
| `games` | GamesTest, ExpansionsTest | ✅ Yes | List games and expansions |
| `products` | ProductsTest, ArticlesTest, MetaproductsTest, PricesTest | ✅ Yes | Search products and prices |
| `stock` | StockTest | ❌ **No** | Add/update/delete stock items |
| `orders` | OrdersTest | ✅ Yes | List orders |
| `wantslists` | WantslistsTest | ❌ **No** | Create/modify/delete wantslists |
| `account` | AccountTest, MessagesTest | ⚠️ Partial | Account info, messages |
| `cart` | CartTest | ❌ **No** | Shopping cart operations |
| `users` | UsersTest | ✅ Yes | User search |

## Configuration

| Variable | Required | Description |
|----------|----------|-------------|
| `CARDMARKET_APP_TOKEN` | ✅ | Your application token |
| `CARDMARKET_APP_SECRET` | ✅ | Your application secret |
| `CARDMARKET_ACCESS_TOKEN` | ✅ | Your access token |
| `CARDMARKET_ACCESS_SECRET` | ✅ | Your access secret |
| `TEST_GAME_ID` | ❌ | Game ID for testing (default: 1 = MTG) |
| `TEST_PRODUCT_ID` | ❌ | Product ID for testing (default: 273799) |
| `TEST_EXPANSION_ID` | ❌ | Expansion ID for testing (default: 1525) |
| `TEST_USER_ID` | ❌ | Your user ID |
| `TEST_OTHER_USER_ID` | ❌ | Another user ID for message tests |

## File Structure

```
e2e/
├── .env.example      # Environment template (versioned)
├── .env              # Your credentials (NOT versioned!)
├── bootstrap.php     # Initialization and helpers
├── TestCase.php      # Base test class
├── run-tests.php     # Test runner
├── README.md         # This file
└── Tests/
    ├── GamesTest.php
    ├── ExpansionsTest.php
    ├── ProductsTest.php
    ├── ArticlesTest.php
    ├── MetaproductsTest.php
    ├── PricesTest.php
    ├── StockTest.php
    ├── OrdersTest.php
    ├── WantslistsTest.php
    ├── AccountTest.php
    ├── MessagesTest.php
    ├── UsersTest.php
    └── CartTest.php
```

## Notes

- Tests that modify data (stock, wantslists, cart) are protected and require explicit ENABLE_* environment variables.
- E2E test articles are marked with comments containing "E2E Test" for easy identification and cleanup.
- E2E wantslists are prefixed with "E2E" for easy identification.
