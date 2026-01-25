# Cardmarket API 2.0 - Main Documentation

## Cardmarket RESTful API Documentation (Version 2.0)

Welcome to the first release version of Cardmarket's API 2.0.

This is the main documentation for Cardmarket's RESTful API version 2.0 (release 1).

If you're using the API version 1.1 (current build 1.1.1), please refer to the 1.1 Documentation.

Document written on September, 30th 2015

---

## Introduction

Welcome to the first release version of Cardmarket's API 2.0.

Read more about the Sandbox and its requirements at the Sandbox Server Documentation.

## Access the API and Authentication

With the introduction of the 2.0 version all versions of the API will run parallel on our servers.

Once 2.0 hits the live servers, we'll discontinue support for 1.0, while support for 1.1 will last until the next major API version. Nevertheless you can still access all 1.0 requests as normal.

With 1.1 we changed the way of authentication against the API and everything you need to know about our implementation of OAuth and how to use it, you'll find in the dedicated Authentication Documentation.

To create an app, login to your account at the normal (sandbox) website and go to your profile page. Here you'll find the option for creating one.

## Types of Resources

Cardmarket API and its resources is basically parted into the following sections:

- **Account Management**
- **Market Place Information**
- **Order Management**
- **Shopping Cart Manipulation**
- **Stock Management**
- **Wants List Management**
- **Cardmarket/API Services**

You'll find an overview description of the different requests (and the links to the detailed description) at the respective section pages.

## Response Formats and Entities

With 1.1 we introduced JSON as an additional response format for all requests. You can choose between XML (still standard) and JSON. If you decide for JSON, simply use the switch `output.json` between the base URI and the resource URL.

### Examples

- `GET https://sandbox.mkmapi.eu/ws/v2.0/games` will return Cardmarket's supported games as XML (because XML is standard).
- `GET https://sandbox.mkmapi.eu/ws/v2.0/output.xml/games` will return Cardmarket's supported games as XML.
- `GET https://sandbox.mkmapi.eu/ws/v2.0/output.json/games` will return Cardmarket's supported games as JSON.

### Entities

You'll find a list of all entities that are returned by the various requests at Entities Documentation. 

With 2.0 we not only introduce HATEOAS links within each response entity, but also changed the structure of entities themselves. In most cases 2.0 entities are not compatible with the 1.1 (and 1.0), so before switching to the 2.0 requests, make sure your app and processing response entities is revisited.

## Response Codes

See API Responses and Status Codes documentation for detailed information about HTTP status codes returned by the API.

## API Sections

### 1. Account Management
Manage authenticated user's account, change settings, handle messages, redeem coupons, and manage user registration/activation.

### 2. Market Place Information
Access information about games, expansions, products, articles, metaproducts, users, and marketplace data.

### 3. Order Management
Retrieve and manage orders as buyer or seller, change order states, add tracking numbers, and evaluate orders.

### 4. Shopping Cart Manipulation
⚠️ **Note:** Shopping Cart API is only available for Widget applications running within the Cardmarket interface. Not available for Dedicated or 3rd Party apps.

### 5. Stock Management
Manage your stock - add, edit, delete articles, retrieve stock information, export stock data, and handle stock in shopping carts.

### 6. Wants List Management
Create and manage wants lists, add/edit/delete items from wants lists, and retrieve wants list information.

### 7. Cardmarket/API Services
Additional services provided by the API including captcha verification and other utility functions.

## Base URLs

### Production
```
https://api.cardmarket.com/ws/v2.0/
```

### Sandbox (Testing)
```
https://sandbox.mkmapi.eu/ws/v2.0/
```

## Request Formats

### XML (Default)
```
GET https://api.cardmarket.com/ws/v2.0/games
GET https://api.cardmarket.com/ws/v2.0/output.xml/games
```

### JSON
```
GET https://api.cardmarket.com/ws/v2.0/output.json/games
```

## Authentication

All API requests (except some 3rd Party endpoints) require OAuth 1.0a authentication.

See the Authentication documentation for detailed information about:
- Creating applications (Dedicated, Widget, 3rd Party)
- App Token and App Secret
- Access Token and Access Secret
- OAuth signature generation
- Authorization headers

## Rate Limits

Request limits reset every midnight at 12am (0:00) CET/CEST:

- **Dedicated App (private users)**: 5,000 requests/day
- **Dedicated App (commercial users)**: 100,000 requests/day
- **Dedicated App (powerseller users)**: 1,000,000 requests/day
- **Widget and 3rd Party Apps**: No request limits

## Response Headers

API responses include helpful headers:

- `X-Request-Limit-Max` - Your request limit
- `X-Request-Limit-Count` - Current number of requests made
- `Access-Control-Allow-Methods` - Allowed HTTP methods for the resource
- `Content-Type` - Response format (application/xml or application/json)

## HATEOAS

API 2.0 implements HATEOAS (Hypermedia as the Engine of Application State) principles. Each entity returned from the API contains links to related resources, allowing you to discover and navigate the API without hardcoding URLs.

Example:
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

## Pagination

Many endpoints support pagination using query parameters:

- `start` - Starting index (0-based)
- `maxResults` - Maximum number of results to return

Recommended values:
- `maxResults=100` to avoid 307 redirects
- `start=0` for the first page

## Version Compatibility

**Breaking Changes from 1.1 to 2.0:**
- Entity structures have changed
- HATEOAS links added to all entities
- Some endpoint URLs may have changed
- Response format differences

**Migration Notes:**
- Test thoroughly in Sandbox environment before production
- Update entity parsing code for 2.0 structure
- Review authentication if migrating from pre-1.1
- Check for deprecated endpoints

## Support and Resources

- **Sandbox Environment**: Test your application without affecting production data
- **API Documentation**: Detailed documentation for all endpoints
- **Entity Documentation**: Complete entity structure reference
- **Authentication Guide**: OAuth implementation details

## Best Practices

1. **Use Sandbox for testing** - Always test in sandbox before production
2. **Implement error handling** - Handle all HTTP status codes appropriately
3. **Respect rate limits** - Monitor X-Request-Limit headers
4. **Use pagination** - For large datasets, always paginate
5. **Cache responses** - Cache product/game data that doesn't change often
6. **Follow HATEOAS links** - Use provided links instead of hardcoding URLs
7. **Handle 307 redirects** - Manually create new OAuth signature for redirects
8. **Set correct Content-Type** - Use application/xml for XML requests
9. **Monitor API updates** - Stay informed about API changes and deprecations
10. **Log API errors** - Keep detailed logs for debugging OAuth and validation issues
