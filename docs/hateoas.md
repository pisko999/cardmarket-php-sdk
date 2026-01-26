# Cardmarket API 2.0 - HATEOAS

## HATEOAS (Hypermedia as the Engine of Application State)

HATEOAS is a constraint of the REST application architecture that distinguishes it from other network application architectures.

With HATEOAS, a client interacts with a network application whose application servers provide information dynamically through hypermedia. A REST client needs little to no prior knowledge about how to interact with an application or server beyond a generic understanding of hypermedia.

## Implementation in Cardmarket API 2.0

Starting with API 2.0, all entities returned by the Cardmarket API include HATEOAS links. These links provide information about:

- Related resources
- Available actions on the current resource
- Navigation paths through the API

## Link Structure

Each link contains:
- **rel** - The relationship type (e.g., "self", "articles", "product")
- **href** - The URI path to the related resource
- **method** - The HTTP method to use (GET, POST, PUT, DELETE)

## Examples

### Product Entity with Links

```json
{
  "product": {
    "idProduct": 100569,
    "name": "Black Lotus",
    "categoryName": "Magic Single",
    "idGame": 1,
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
      },
      {
        "rel": "expansion",
        "href": "/expansions/1",
        "method": "GET"
      }
    ]
  }
}
```

### Order Entity with Links

```json
{
  "order": {
    "idOrder": 12345678,
    "state": "paid",
    "links": [
      {
        "rel": "self",
        "href": "/order/12345678",
        "method": "GET"
      },
      {
        "rel": "send",
        "href": "/order/12345678",
        "method": "PUT"
      },
      {
        "rel": "tracking",
        "href": "/order/12345678/tracking",
        "method": "PUT"
      }
    ]
  }
}
```

## Common Link Relations

### Resource Navigation
- **self** - Link to the current resource
- **parent** - Link to the parent resource
- **collection** - Link to the collection this resource belongs to

### Related Resources
- **product** - Link to product details
- **articles** - Link to available articles for a product
- **expansion** - Link to expansion details
- **game** - Link to game details
- **user** - Link to user profile
- **order** - Link to order details

### Actions
- **send** - Mark order as sent
- **tracking** - Add tracking number
- **evaluation** - Evaluate order
- **cancel** - Cancel order

## Benefits of HATEOAS

### 1. API Discoverability
Clients can discover available actions and related resources without prior knowledge of the API structure.

### 2. Reduced Coupling
Clients don't need to hardcode URLs. They follow links provided by the server, making the API more flexible to changes.

### 3. Self-Documenting
Links provide information about what actions are available on a resource and how to access related resources.

### 4. State-Based Actions
Links can change based on the current state of a resource. For example, an order in "paid" state will have different available actions than an order in "sent" state.

## Using HATEOAS Links

### Instead of Hardcoding URLs

❌ **Don't do this:**
```php
$articlesUrl = "https://apiv2.cardmarket.com/ws/v2.0/articles/" . $productId;
```

✅ **Do this:**
```php
// Get product first
$product = $api->getProduct($productId);

// Find the articles link
foreach ($product['links'] as $link) {
    if ($link['rel'] === 'articles') {
        $articlesUrl = $link['href'];
        $articlesMethod = $link['method'];
        break;
    }
}

// Use the discovered link
$articles = $api->request($articlesMethod, $articlesUrl);
```

### State-Based Navigation

```php
// Get order
$order = $api->getOrder($orderId);

// Check what actions are available based on current state
$availableActions = [];
foreach ($order['links'] as $link) {
    if ($link['method'] === 'PUT' || $link['method'] === 'POST') {
        $availableActions[] = $link['rel'];
    }
}

// Now you know what actions you can perform:
// e.g., ['send', 'tracking', 'cancel']
```

## XML Format

HATEOAS links are also available in XML responses:

```xml
<product>
  <idProduct>100569</idProduct>
  <name>Black Lotus</name>
  <links>
    <link>
      <rel>self</rel>
      <href>/products/100569</href>
      <method>GET</method>
    </link>
    <link>
      <rel>articles</rel>
      <href>/articles/100569</href>
      <method>GET</method>
    </link>
  </links>
</product>
```

## Best Practices

1. **Always check for links** - Don't assume a link exists; always verify it's in the response
2. **Follow link relations** - Use the `rel` attribute to identify the link you need
3. **Respect the method** - Use the HTTP method specified in the link
4. **Handle missing links** - Some links may not be present based on state or permissions
5. **Don't parse URIs** - Treat URIs as opaque strings; don't parse or construct them
6. **Cache link structure** - You can cache the link structure for frequently accessed resources

## Limitations and Notes

- Not all historical endpoints have full HATEOAS support
- Some actions may require additional request body parameters not specified in links
- Links are relative paths; prepend with base URL and version
- OAuth signature must be recalculated for each discovered URL
- Links may change between API versions; don't cache across versions

## Further Reading

- REST architectural style
- Richardson Maturity Model (Level 3 - Hypermedia Controls)
- Roy Fielding's dissertation on REST
