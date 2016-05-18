Request converter for Symfony2
=================
[![Build Status](https://travis-ci.org/nikita2206/symfony-request-converter.svg?branch=master)](https://travis-ci.org/nikita2206/symfony-request-converter)
[![Coverage Status](https://coveralls.io/repos/github/nikita2206/symfony-request-converter/badge.svg?branch=master)](https://coveralls.io/github/nikita2206/symfony-request-converter?branch=master)

Convert API requests from plain PHP-arrays to request objects.


### Example

In your controller action you can have: 
```php
public function addBlogPostAction(PostRequest $post)
{
    $this->commandBus->execute(new NewPost($post));
}
```

Request will automatically be converted from plain array form to the
`PostRequest` class instance (created without calling the constructor) and validated:
```php

use RequestConverter\Annotation\Type;
use RequestConverter\Annotation\Optional;
use RequestConverter\Annotation\Request;

/**
 * @Request()
 **/
class PostRequest
{
    /**
     * @Type("string")
     **/
    private $title;
    
    /**
     * @Type("string")
     **/
    private $text;
    
    /**
     * @Type("string")
     * @Optional()
     **/
    private $author;
}
```

### How to set up

After requiring `nikita2206/symfony-request-converter` in your `composer.json` you'll need to register
  [exception listener](http://symfony.com/doc/current/cookbook/event_dispatcher/event_listener.html)
  in order to render `RequestConverter\Exception\BadRequestException` which will be thrown if the request isn't valid.

This is a basic example of said listener, you'll of course want to make it adhere to your API error specs. Or, as
 a better solution, delegate rendering of an error to another controller just like it's done in
 `Symfony\Component\HttpKernel\EventListener\ExceptionListener`.

```php
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use RequestConverter\Exception as Ex;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if ( ! $event->getException() instanceof Ex\BadRequestException) {
            return;
        }

        $errors = [];

        // this exception is thrown when there was a problem while mapping input data on the object:
        //   * missing field
        //   * incompatible type (string when array is expected f.e.)
        //   * uncoercible value (string "45zz" when integer is expected)
        if ($event->getException() instanceof Ex\RequestConversionException) {
            foreach ($event->getException()->getErrors() as $err) {
               $errors[] = ["field" => $err->getField(), "kind" => get_class($err)];
            }

        // this exception is the result of symfony/validation component, it has ConstraintViolationListInterface
        } elseif ($event->getException() instanceof Ex\RequestValidationException) {
            foreach ($event->getException()->getViolations() as $v) {
                $errors[] = ["field" => $v->getPropertyPath(), "kind" => $v->getMessage()];
            }
        }

        $event->setResponse(new Response(\json_encode($errors), 400));
    }
}
```

### How to use

All root-request classes will need to be marked with the `RequestConverter\Annotation\Request` annotation. And
  every member of said request class can be marked with the `RequestConverter\Annotation\Type` annotation in order to
  force it to be coerced to said type, below is the reference on all possible types. Also you can
  mark them with the `RequestConverter\Annotation\Optional` annotation to ignore the absence of the field in the
  request payload.

#### Type annotation

There are several types you can use, those are: int, bool, float, string, array, map and or a class name.
If the incoming data is not of the right type, we will try to coerce it if possible, here's a list of possible
types and their coercion compatibility with others.

###### Int type

Apart from `int` it can take a numeric `string` that represents integer. Otherwise if it's a text string or if it
  represents float, you'll get `UncoercibleValueError`.

###### Bool type

Can also accept `int`, `float` and `string`.
For `int` and `float` as well as for numeric strings will yield `false` for `0` and `true` for all other numbers.
For non-numeric strings it will first try to compare them with the preset values of `[yes, true, Y, T]` and
  `[no, false, N, F]`, if the match isn't found it should silently cast a string with `(bool)$value`.

###### Float type

Can also accept `int` and `string`. Will try to coerce `string` to `float` if the string represents float/integer value.

###### String type

Will accept `string`, `int` and `float` and will cast it to `string`.

###### Array type

Accepts array only.

This type is parametrized: for an array of `T` you can use `array<T>` syntax (resembles syntax for generics in
  popular languages). If the `T` is a class name it will be mapped and validated as well.

###### Map type

Accepts array only.

This type is parametrized and has two overloads: `Map<V>` and `Map<K, V>`.
The `K` type parameter can only be either `string` or `int`.

###### Class name type

If you use your own class name in the `RequestConverter\Annotation\Type` annotation or as an argument to the `array`
  or `Map` types, RequestConverter will try to map input data to the newly created object of said class.

#### Validation

You can also use validation constraints for `symfony/validator` component. If conversion goes without errors the
  validator is applied with the default validation group.

### Extending RequestConverter

You can add your own data types to be used with `Type` annotation and parametrized types - for that you'll need to
  create a new implementation of `RequestConverter\Coercion\TypeCoercer` interface and register it in the
  `RequestConverter\Coercer` service. Currently this service is not configurable so you'll have to use Symfony
  DI's feature of overriding service definitions and override the definition of the `request_converter.coercer` service.

### Contributing

All kinds of contributions are welcome: documentation, code or tests. The project mainly adheres to PSR-2 style guide.

You can run tests with `./vendor/bin/phpunit` command from the root of the project.
