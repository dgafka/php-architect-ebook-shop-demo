# php-architect-repairing-house

This repository provides real implementation of topic discussed in Article from PHP Architect.  

## Reminder of what we are building

We will build E-Book Shop:

- We will be able to register new e-books for given price.
- Users can order e-books and pay for it with Credit Card.
- Users are able to place orders by simply providing their Email Address
- Once payment is successful, we will send e-mail containing e-books.
- After 3 successful orders we will grant user with promotion for 10% decrease for all next orders

## Implementations

1. `1-version-sql (DBAL)`  
This provides implementation using SQLs only
2. `2-version-model (DDD,Aggregates,Value Objects)`  
This provides implementation by introducing Model to make application more maintainable. 
3. `3-version-messaging (CQRS,Events,Asynchronicity)`  
This provides implementation that is extended by Messaging architecture to make application more solid and stable.

## Configuration

Run `composer install` inside any of the catalogs and then execute application

## Run the application

1. Run database migrations `vendor/bin/doctrine-migrations migrations:migrate --no-interaction`
2. Run the example `run_example.php`
