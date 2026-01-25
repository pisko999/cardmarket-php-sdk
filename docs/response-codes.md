# Cardmarket API 2.0 - Response Codes

## HTTP Status Codes

### 200 OK

Any successful request normally returns a `200 OK` HTTP status.

### 206 Partial Content and 204 No Content

As announced we change the strategy of `206 Partial Content` HTTP status responses. You now have full control if receiving the whole collection or only parts of it.

### 307 Temporary Redirect

Particular requests can deliver thousands of entities (e.g. a large stock or requesting articles for a specified product, and many more). Generally all these requests allow you to paginate the results - either returning a 206 or 204 HTTP status code. Nevertheless, all these requests can also be done without specifying a pagination.

If done and the resulting entities would be more than 1,000 (or 100) the request will respond with a 307, specifying the paginated request. However, you should switch off the behaviour to automatically redirect to the given request URI, because a new `Authorization` header needs to be compiled for the redirected resource.

Unfortunately we were forced to 307 the following requests:

- GET Articles
- GET User Articles
- GET Filter Orders
- GET Stock

As general values for force-paginated requests please use `maxResults=100` in order to avoid being 307'd again. The first result is indexed with 0: `start=0`.

**Attention:** Some implementations try to perform a 307 Temporary Redirect instantly and without further notice. This will lead to a 401 Unauthorized (or 403 Forbidden), because the OAuth signature for the original request is not valid for the request delivered with the 307 Temporary Redirect.

### 400 Bad Request

Whenever something goes wrong with your request, e.g. your POST data and/or structure is wrong, or you want to access an article in your stock by providing an invalid ArticleID, a `400 Bad Request` HTTP status is returned, describing the error within the content.

### 401 Unauthorized

You get a `401 Unauthorized` HTTP status, when authentication or authorization fails during your request, e.g. your `Authorization` (signature) is not correct.

### 403 Forbidden

You get a `403 Forbidden` HTTP status, when you try to access valid resources, but don't have access to it, i.e. you try to access `/authenticate` with a dedicated or widget app, or resources specifically written for widget apps with a dedicated app.

### 405 Not Allowed

You get a `405 Not Allowed` HTTP status, every time you want to access a valid resource with a wrong HTTP method.

While `OPTIONS` requests are now possible on all of the API's resources, most resources are limited to one or more other HTTP methods. These are always specified in the `Access-Control-Allow-Methods` header coming with each response. Please refer to CRUD Operations Documentation to learn more about the different HTTP methods and which purposes they fulfill in a RESTful API.

### 412 Precondition Failed

When you want to perform an invalid state change on one of your orders, e.g. confirm reception on an order, that's still not flagged as sent, you get a `412 Precondition Failed` HTTP status.

### 417 Expectation Failed

Typically you get a `417 Expectation Failed` HTTP status code, when your request has an XML body without the corresponding header and/or the body not sent as text, but its byte representation. Please also don't send any `Expect:` header with your request.

### 429 Too Many Requests

Our API has the following request limits which reset every midnight at 12am (0:00) CET/CEST:

- **Dedicated App (private users):** 5,000
- **Dedicated App (commercial users):** 100,000
- **Dedicated App (powerseller users):** 1,000,000
- **Widget and 3rd-Party Apps:** don't have any request limits

If your app has a request limit, additional response headers are sent by the API:

- `X-Request-Limit-Max`, which contains your request limit
- `X-Request-Limit-Count`, which contains the actual number of requests you made after the last request limit reset

Once your request limit is reached the API will answer with a `429 Too Many Requests` until the next request limit reset.
