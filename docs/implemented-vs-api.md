# SDK Implementation vs. API Documentation Comparison

Date: January 25, 2026

## ✅ IMPLEMENTED (coverage ~95%)

### 1. Account Management ✅ COMPLETE

| API Endpoint | SDK Method | File |
|--------------|-----------|------|
| GET Account Information | `getAccountInformation()` | AccountResource.php |
| PUT Change Vacation Status | `setOnVacation()` | AccountResource.php |
| PUT Change Display Language | `setDisplayLanguage()` | AccountResource.php |
| GET Messages | `getMessagesThread()`, `getMessageByUser()`, `getMessagesThreadByUser()` | MessagesResource.php |
| POST Messages | `sendMessage()` | MessagesResource.php |
| DELETE Messages | `deleteMessagesByUser()`, `deleteOneMessageByUser()` | MessagesResource.php |
| GET Find Messages | `findMessages()` | MessagesResource.php |
| POST Redeem Coupons | `redeemCoupons()` | CouponResource.php |

**Note:** Deprecated APIs (Request Seller Activation, Register New User, Activate New User, Request Login Data, Logout) are not implemented - not needed for Dedicated/Widget apps.

---

### 2. Market Place Information ✅ NEARLY COMPLETE

| API Endpoint | SDK Method | File | Status |
|--------------|-----------|------|--------|
| GET Games | `getGamesList()` | GamesResource.php | ✅ |
| GET Expansions | `getExpansionsListByGame()` | ExpansionsResource.php | ✅ |
| GET Expansion Singles | `getCardsListByExpansion()` | ExpansionsResource.php | ✅ |
| GET Products | `getProductDetails()` | ProductsResource.php | ✅ |
| GET Product List (File) | `getProductListFile()` | ProductsResource.php | ✅ |
| GET Price Guides (File) | `getPriceGuideFile()` | PricesResource.php | ✅ |
| GET Find Products | `findProducts()` | ProductsResource.php | ✅ |
| GET Articles | `getArticles()` | ArticlesResource.php | ✅ |
| GET Metaproducts | `getMetaProductDetails()` | MetaproductsResource.php | ✅ |
| GET Find Metaproducts | `findMetaProducts()` | MetaproductsResource.php | ✅ |
| GET Users | `getUserDetails()` | UsersResource.php | ✅ |
| GET Find Users | `findUsers()` | UsersResource.php | ✅ |
| GET User Offers | `getArticlesByUser()` | ArticlesResource.php | ✅ |
| GET Export User Offers | `getRequestedUserOffersById()` | UsersResource.php | ✅ |
| POST Export User Offers | `requestExportUserOffersById()` | UsersResource.php | ✅ |
| GET List Requested Exports | `getExportUserOffersList()` | UsersResource.php | ✅ |

---

### 3. Order Management ✅ COMPLETE

| API Endpoint | SDK Method | File |
|--------------|-----------|------|
| GET Orders | `getOrder()` | OrdersResource.php |
| PUT Orders | `changeOrderState()` | OrdersResource.php |
| PUT Tracking Number | `setOrderTrackingNumber()` | OrdersResource.php |
| POST Evaluate an Order | `evaluateOrder()` | OrdersResource.php |
| GET Filter Orders | `getOrders()`, `getSentOrders()`, `getReceivedOrders()` | OrdersResource.php |

---

### 4. Shopping Cart Manipulation ✅ COMPLETE

| API Endpoint | SDK Method | File |
|--------------|-----------|------|
| GET Shopping Cart | `getCart()` | CartResource.php |
| PUT Shopping Cart | `add()` (+ `setAction()`) | CartResource.php |
| DELETE Shopping Cart | `emptyCart()` | CartResource.php |
| PUT Checkout | `checkout()` | CartResource.php |
| PUT Shipping Address | `setCartAddress()` | CartResource.php |
| GET Shipping Method | `getShippingMethods()` | CartResource.php |
| PUT Shipping Method | `setShippingMethod()` | CartResource.php |

---

### 5. Stock Management ✅ COMPLETE

| API Endpoint | SDK Method | File |
|--------------|-----------|------|
| GET Stock | `getStock()` | StockResource.php |
| POST Stock | `add()` | AddArticleStockResource.php |
| PUT Stock | `send()` | UpdateArticleStockResource.php |
| DELETE Stock | `send()` | DeleteArticleStockResource.php |
| GET Stock (File) [DEPRECATED] | `getStockFile()` | StockResource.php |
| GET Stock in Shopping Carts | `getArticlesListInUsersShoppingCarts()` | StockInShoppingCartsResource.php |
| GET Stock Article | `getStockArticle()` | StockResource.php |
| GET Find Stock Articles | `findStockArticles()` | StockResource.php |
| PUT Change Stock Article Quantity | `increaseStock()`, `decreaseStock()` | StockResource.php |
| GET Stock Articles of Product | `getStockArticlesOfProduct()` | StockResource.php |
| GET Export Your Stock | `getStockExportStatus()` | StockExportResource.php |
| POST Export Your Stock | `askStockExport()` | StockExportResource.php |
| GET Stock Export Details | `getStockExportStatus()` | StockExportResource.php |

---

### 6. Wants List Management ✅ COMPLETE (newly implemented)

| API Endpoint | SDK Method | File |
|--------------|-----------|------|
| GET Wants Lists | `getWantsLists()` | WantsListResource.php |
| POST Wants Lists | `createWantsList()` | WantsListResource.php |
| GET Wants Lists Items | `getWantsList()` | WantsListResource.php |
| PUT Wants Lists Items | `renameWantsList()`, `addItemsToWantsList()`, `editItemsInWantsList()`, `deleteItemsFromWantsList()` | WantsListResource.php |
| DELETE Wants Lists Items | `deleteWantsList()` | WantsListResource.php |

---

### 7. MKM/API Services

| API Endpoint | SDK Method | File | Status |
|--------------|-----------|------|--------|
| GET Generate a Captcha [DEPRECATED] | `generateCaptcha()` | CaptchaResource.php | ⚠️ Deprecated |

---

## ❌ NOT IMPLEMENTED (3rd party apps only)

The following endpoints are available **ONLY for 3rd party applications** and are not relevant for Dedicated/Widget apps:

### Account Management (3rd party only)
- ❌ `POST Register New User Account` - User registration
- ❌ `GET Activate New User Account` - Request activation code
- ❌ `POST Activate New User Account` - Account activation
- ❌ `POST Request Login Data` - Request login credentials
- ❌ `POST Logout` - User logout

**Reason for non-implementation:** These endpoints are exclusively intended for 3rd party applications that manage user accounts externally. Dedicated and Widget apps use OAuth authentication directly through Cardmarket.

---

## 📊 Coverage Statistics

### By category:
- **Account Management**: 8/13 endpoints (62%) - 5 endpoints for 3rd party only
- **Market Place Information**: 16/16 endpoints (100%)
- **Order Management**: 5/5 endpoints (100%)
- **Shopping Cart Manipulation**: 7/7 endpoints (100%)
- **Stock Management**: 13/13 endpoints (100%)
- **Wants List Management**: 5/5 endpoints (100%)
- **MKM/API Services**: 1/1 endpoints (100% - deprecated)

### Total coverage for Dedicated/Widget apps:
- **Implemented**: 55 endpoints
- **Relevant endpoints**: 55 endpoints (after excluding 3rd party only)
- **Coverage**: **100% of all relevant endpoints** ✅

### Notes:
1. ✅ All endpoints available for Dedicated and Widget apps are implemented
2. ⚠️ 1 implemented endpoint is deprecated (Captcha)
3. ❌ 5 non-implemented endpoints are available ONLY for 3rd party apps
4. 🎯 SDK provides **complete coverage** for typical usage

---

## 🔍 Detailed Analysis

### Deprecated functions in API:
- `Request Seller Activation` (POST, PUT) - not implemented
- `Stock (File)` (GET) - **implemented** as `getStockFile()`
- `Generate a Captcha` (GET) - **implemented** as `generateCaptcha()`

### Added utility methods (beyond API):
- `getSentOrders()` - helper for `getOrders('seller', 'sent')`
- `getReceivedOrders()` - helper for `getOrders('buyer', 'received')`
- `setAction()` / `getAction()` - helpers for cart operations

### Entity support:
SDK provides complete entities for all API objects:
- ArticleEntity, ArticleBaseEntity
- WantslistEntity, WantslistItemEntity, WantslistItemsEntity
- CartAddressEntity, CartArticlesEntity
- MessageEntity, EvaluationEntity
- CouponEntity, ShippingMethodEntity
- TrackingNumberEntity, OrderChangeStateEntity
- MultipleEntity (for batch operations)

---

## ✨ Conclusion

SDK provides **100% coverage of all API endpoints** relevant for Dedicated and Widget applications.

Non-implemented endpoints (user registration, logout, etc.) are available only for 3rd party applications and are not needed for typical SDK usage with OAuth authentication.
