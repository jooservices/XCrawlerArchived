# Test Guide

## Description
All tests are split into 4 major suites. Each suite is reflecting the architecture of the application.
```
- Unit
    - Models
    - Services
    - Observers
- Feature
    - Console
    - Jobs
- Integration
- External
```

### Unit Tests
Unit Testing ensures functions, classes and methods are working as expected by checking values going in and out of various functions and methods. By using Dependency Injection and building “mock” classes and stubs you can verify that dependencies are correctly used for even better test coverage.
Unit tests assert relationships of the Models and single methods of Services/Managers. You should assert against the database anytime you are performing a transaction to make sure you are sending valid arguments.

### Feature Tests
Feature tests are meant to check the endpoints, jobs and console commands. Middleware and permissions should also be tested. You should assert the response status, the json structure and if necessary assert against the database.

### Integration Tests
Integration tests are a simulation of a business logic workflow. Typically, you set up the test, you perform different steps and assert each one is giving you the expected results. This is like a combination of unit and feature tests.

### External Tests
External tests are related to the API/SDK that we use. Create interfaces for them in Unit tests, and mock (if possible) the external responses. All these tests should be grouped in order that they are excluded by default when running the entire suite.
