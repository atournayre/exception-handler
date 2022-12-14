# Exception Handler Component for Symfony

This component helps handle exceptions inside Symfony.

It is inspired from the article [Centralized exception handling with Symfony and custom PHP attributes
](https://angelovdejan.me/2022/11/24/centralized-exception-handling-with-symfony-and-custom-php-attributes.html) by [Dejan Angelov](https://github.com/angelov). Read it before!

## Install
### Composer
```shell
composer require atournayre/exception-handler
```

### Usage

1. Define `HttpStatusCodeExceptionHandler` as a listener in your application.
2. Declare attributes on your existing/new exceptions classes.
3. Remove (or do not catch exception) in your controller.
4. It works!

#### Define `HttpStatusCodeExceptionHandler` as a listener in your application
```yaml
# config/services.yaml
services:
  Atournayre\Component\ExceptionHandler\Handler\HttpStatusCodeExceptionHandler:
    tags:
      - { name: kernel.event_listener, event: kernel.exception }
```
#### Declare attributes on your existing/new exceptions classes.
```php
// Example for a NotFound (404)
#[NotFound]
class OrderNotFound extends Exception
{
    public function __construct(OrderId $id)
    {
        parent::__construct(
            sprintf('The order "%s" could not be found.', (string) $id)
        );
    }
}
```
```php
// Example for a UnprocessableEntity (422)
#[UnprocessableEntity]
class OrderAlreadyShipped extends Exception { ... }
```
```php
// Example for a Forbidden (403)
#[Forbidden]
class CustomerMismatch extends Exception { ... }
```


## Contributing
Of course, open source is fueled by everyone's ability to give just a little bit
of their time for the greater good. If you'd like to see a feature or add some of
your *own* happy words, awesome! Tou can request it - but creating a pull request
is an even better way to get things done.

Either way, please feel comfortable submitting issues or pull requests: all contributions
and questions are warmly appreciated :).
