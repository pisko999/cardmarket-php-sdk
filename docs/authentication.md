# Cardmarket API - Authentication and OAuth

## How to get Access to the Cardmarket API and Authentication Overview

### Creating an App

Generally, to get access to the API, you first need to create an App in your user profile. You can only create and have one app at the same time. There are two different app types - all serving its own purpose.

**Attention:** API applications and access are restricted to professional sellers and subject to a manual approval process at this moment.

The two different apps and their designated purposes are:

#### 1. Dedicated App

If you want to develop an application that provides services only to your very own account at MKM, this app type is your solution. Your app is acting with the API like you are acting with your user account using the website.

Once a dedicated app was registered, you'll find everything you need in your profile to make requests to the API. You get an **App token**, **App Secret**, **Access token**, and **Access token secret**.

**Attention:** We explicitly do not allow that Dedicated App users constantly only request the public Marketplace resources (products, articles, prices, etc.) on consecutive days and especially not with exhausting the request limits. The purpose of Dedicated Apps is the support of the user with their normal MKM activities, which explicitly includes managing their stock, order, wantslists, and handling their shopping carts. We implemented extensive security mechanisms to withdraw a user's possibility to request all resources by blocking a user's API access.

#### 2. 3rd Party App

If you want to develop an application that provides services to all other active MKM users and let them use the functionality through the API with your app, e.g. you retrieve their stock and let them manipulate their stock, this app type is your solution.

Only users with a commercial account can apply for and register 3rd Party apps. These apps need to be verified by us. So, first you have to request the registration of a 3rd Party App. After we approved your request, you find everything you need in your profile to make requests to the API in your profile. You get an **App token** and **App Secret**. You need to get **Access Token** and **Access Token Secret** from each user via OAuth flow.

**Reason:** When applying for a 3rd Party App you need to provide a reason, i.e. explaining why you need that type of app. The more detailed you describe your project the more likely we'll approve that app or get in contact with you when things are still unclear. If the provided reason is less meaningful, we'll most likely reject the application.

**Attention:** Changing the type of an application is not possible, i.e. you can't switch your Dedicated App to a 3rd Party App. Instead you need to create a new one, and let it get approved eventually.

### Revoking API Access

We have security mechanisms installed that revoke a user's API access after detecting possible abusive use.

You can generally request API access being granted again at the end of the month following the month of the revocation. As an example, if your account was blocked from using the API due to possible abuse in February 2019, you can request API access again at the end of March 2019.

The decision about that is taken based on your marketplace activities in the period between the block and your request.

## Process Overview

### For 3rd Party Apps:
1. An MKM user wants to access MKM via an App
2. The App redirects to a login site on MKM, identifying itself with an App Token
3. The user logs in at the MKM site, which checks their credentials
4. After MKM successfully authenticated the user, MKM redirects to the App's callback URI, providing a Request Token
5. The App now needs to trade the Request Token for an Access Token and an Access Token Secret from the API
6. The App now has everything to access protected resources for the authenticated user

### Tokens Explained:

**App Token** - After successful registration of your app (resp. after our approval of a 3rd party app) you'll find the App Token (also known as Consumer Key) in your profile. You need that token to identify your app for your requests.

**App Secret** - Together with the App Token you also find an App Secret (also known as Consumer Secret). You'll need that Secret to sign your requests. Please keep the App secret secret!

The combination of **App token** and **App secret** identifies an app to our API.

**Request Token** - After the user successfully authenticated at an MKM Login Site, MKM provides a Request Token to the App's callback URI. Only applicable for 3rd party apps!

**Access Token and Access Token Secret (for 3rd party apps)** - After the App received the Request Token, it needs to trade this token for an Access Token and Access Token Secret. Like the App Token (and App Secret), the Access Token is needed to identify the user, the Access Token Secret is needed to sign the requests. Please note that both the token and the secret only have a limited lifetime.

**Access Token and Access Token Secret (for dedicated apps)** - Once registered, you find both tokens on your profile page. Both have an unlimited lifetime unless you delete or renew them.

Please keep the Access token secret secret!

The combination **Access token** and **Access Token Secret** identifies the MKM user that is accessing MKM through the API.

App Token and App Secret can be regenerated at any time using the profile page. Dedicated Apps can also regenerate their Access Token and Access Token Secret at any time.

## Detailed Process Description

The following steps lead you to detailed descriptions (and code examples) of each step necessary to successfully using the OAuth implementation on the MKM API and fire requests using OAuth.

1. Let the user login on MKM, retrieve the Request Token and exchange it into an Access Token and Access Token Secret - Get an Access Token and Access Token Secret - Only applicable for 3rd party apps!
2. Request a protected resource for the authenticated user - Request a Protected Resource

All requests to MKM's API need to provide an `Authorization` header. How to compile it, you can read at the OAuth Header page.

### Example Values

For all the examples we assume values for the respective tokens:

- **App Token:** `bfaD9xOU0SXBhtBP`
- **App Secret:** `pChvrpp6AEOEwxBIIUBOvWcRG3X9xL4Y`
- **Request Token:** `HSZorctm86Cw6OHKfRkr8xoSlt1SKE5Z`
- **Access Token:** `lBY1xptUJ7ZJSK01x4fNwzw8kAe5b10Q` (either it comes from the `/access` request or is set for a dedicated app; widget apps use an empty string)
- **Access Token Secret:** `hc1wJAOX02pGGJK2uAv1ZOiwS7I9Tpoe` (either it comes from the `/access` request or is set for a dedicated app; widget apps use an empty string)
- **Callback URI:** `http://www.awesomemkm3rdpartyapp.eu/callback.php?request_token=`

## Working Examples

We have compiled simple working examples to show you how to request the API using the OAuth authentication:

- for PHP with the libcurl library (online)
- for Microsoft's .NET Framework with C# (online)
- for Microsoft's .NET Framework with VB.NET (online)
- for Java (special thanks to Antonio73 and itineris, online, currently without syntax highlighting)

For these examples we use:

- the `/account` request from the API 1.1: `GET https://apiv2.cardmarket.com/ws/v1.1/account`
- the App Token, App Secret, Access Token, Access Token Secret shown above
- **Timestamp:** `1407917892`
- **Nonce:** `53eb1f44909d6`

We expect that you can derive these concrete examples to all other requests (including POST, PUT, DELETE) by using the documentations of your programming languages, frameworks, libraries, and tools.

### Elixir Library

Cardmarket user kelostrada has kindly provided us with an Elixir library.

## OAuth Header Construction

All requests to the Cardmarket API must include an `Authorization` header following OAuth 1.0a specification. The header contains:

- OAuth realm
- OAuth consumer key (App Token)
- OAuth token (Access Token)
- OAuth signature method (HMAC-SHA1)
- OAuth timestamp
- OAuth nonce
- OAuth version (1.0)
- OAuth signature

The signature is computed using HMAC-SHA1 algorithm with a signing key composed of your App Secret and Access Token Secret.

## Security Best Practices

1. **Never share your secrets** - Keep App Secret and Access Token Secret confidential
2. **Use HTTPS** - Always use HTTPS for API requests
3. **Regenerate tokens periodically** - For dedicated apps, consider regenerating tokens regularly
4. **Validate requests** - Verify the authenticity of API responses
5. **Handle token expiration** - For 3rd party apps, implement token refresh logic
6. **Monitor API usage** - Keep track of your request limits and patterns
7. **Implement error handling** - Handle authentication errors gracefully (401, 403)
