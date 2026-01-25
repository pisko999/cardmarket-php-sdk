# Cardmarket API 2.0 - Sandbox Server

## Sandbox Server Status

⚠️ **The Sandbox Server is officially abandoned.**

## Historical Information

The Sandbox Server was previously used for testing API implementations without affecting production data. However, as of the current date, the sandbox environment is no longer maintained or available.

## Testing Recommendations

Since the Sandbox Server is abandoned, developers should:

1. **Use test accounts** - Create separate test accounts on the production environment
2. **Small transactions** - Perform small, reversible test transactions
3. **Local mocking** - Implement local API mocking for unit tests
4. **Careful testing** - Be extra careful when testing in production
5. **Read-only first** - Test read operations (GET) before write operations (POST/PUT/DELETE)

## Alternative Testing Strategies

### 1. Mock Responses
Create mock HTTP responses for testing:

```php
// Example: Mock HTTP client for testing
$mockClient = new MockHttpClient([
    new MockResponse(json_encode(['game' => ['idGame' => 1]])),
]);
```

### 2. Test Mode Flag
Implement a test mode in your application:

```php
if ($config['test_mode']) {
    // Use mock data
    return $this->getMockData();
} else {
    // Call real API
    return $this->api->call($endpoint);
}
```

### 3. Separate Test Account
Use a dedicated test account with:
- Minimal stock
- Test products only
- Separate from production operations
- Clear naming (e.g., "TestAccount_DevEnvironment")

### 4. Local Development Environment
Set up a local development environment:
- Mock API responses
- Use fixtures for common scenarios
- Implement API simulators for complex workflows

## Risks of Testing in Production

Be aware of the following risks when testing in production:

- **Rate limits** - Test requests count toward your daily limit
- **Real transactions** - Mistakes affect real users and data
- **Reputation impact** - Poor testing practices may affect seller rating
- **Financial impact** - Errors in pricing or stock can have financial consequences
- **User experience** - Test activities may confuse real users

## Best Practices Without Sandbox

1. **Start with read operations** - GET requests are safe to test extensively
2. **Use small values** - Test with minimal quantities and low prices
3. **Immediate rollback** - Have procedures to quickly reverse test actions
4. **Clear documentation** - Document what was tested and when
5. **Off-peak testing** - Test during low-activity periods
6. **Monitoring** - Watch for unexpected behavior or errors
7. **Version control** - Use version control for API integration code
8. **Gradual rollout** - Test features with limited scope before full deployment

## Migration from Sandbox

If you previously used the Sandbox Server:

1. Remove sandbox URLs from configuration
2. Update to production URLs: `https://api.cardmarket.com/ws/v2.0/`
3. Replace sandbox credentials with production credentials
4. Implement local mocking for unit tests
5. Review and update test procedures
6. Ensure all team members are aware of production-only testing

## Production API URL

```
https://api.cardmarket.com/ws/v2.0/
```

## Support

For questions about testing strategies or API usage:
- Review API documentation
- Contact Cardmarket support
- Join developer community forums
- Check for API updates and announcements

## Historical Note

The sandbox was originally located at:
```
https://sandbox.mkmapi.eu/ws/v2.0/
```

This URL is no longer functional and should not be used.
