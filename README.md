# Datadog Bundle For Symfony4 Apps.

[![Build Status](https://travis-ci.com/theplankmeister/datadog_bundle.svg?branch=master)](https://travis-ci.com/theplankmeister/datadog_bundle) [![Latest Stable Version](https://poser.pugx.org/theplankmeister/datadog_bundle/v/stable)](https://packagist.org/packages/theplankmeister/datadog_bundle) [![License](https://poser.pugx.org/theplankmeister/datadog_bundle/license)](https://packagist.org/packages/theplankmeister/datadog_bundle)

This bundle provides a reasonably simple interface for you to adjust Datadog metrics via a user-defined class, using annotations to declare the metric type, operation, and name, providing your app with code completion, and compatibility with code analysis tools such as PHPStan.

## Installation
In order to install this bundle in your app, simply install using composer:
```bash
$ composer require theplankmeister/datadog_bundle
```

## Configuration
The only config required by default is the `datadog_metric_prefix` parameter in your services file that defines the prefix to use for your metrics.

```yaml
parameters:
    my_param: something
    another_param: something else
    datadog_metric_prefix: metric_prefix
```
## Usage
I'm going to assume you have autowiring enabled in your app. If not, then you know enough about how to manually wire the service for availability. Create a class in your app's namespace that extends `ThePlankmeister\DatadogBundle\AbstractDatadogService`. For example:
_src/Datadog/Stats.php_
```php
<?php
namespace App\Datadog;
use ThePlankmeister\DatadogBundle\AbstractDatadogService;
class Stats extends AbstractDatadogService
{
}
```
This will be made available in your app via autowiring and typehinting with the `App\Datadog\Stats` class. Now you need to declare the metrics and operations to perform on those metrics (and parameters to pass them, if required) using class annotations. An example:
```php
<?php 
/**
 * @method void incFooBarBaz(float $sampleRate = 1.0, array|string|null $tags = null, int $incValue = 1) When Netaxept registration fails
 **/
class Stats extends AbstractDatadogService
{
}
```
The key part of this annotation is the `@method void <method name>([arguments]) [description]`. Notice that the method is named `incFooBarBaz`. The first 3 characters, `inc`, are used to determine that this method will invoke Datadog's `increment` method. The remainder of the method name is assumed to be CamelCase, and will be broken at case boundaries and have dots inserted. Together with the prefix, this means that this method invocation will increment the `metric_prefix.foo.bar.baz` metric. The supported list of Datadog methods and the prefixes to invoke them are listed below.

| Prefix | Datadog method |
| --- | --- |
| inc | increment() |
| dec | decrement() |
| tim | timing() |
| mic | microtiming() |
| gau | gauge() |
| his | histogram() |
| dis | distribution() |
| set | set() |
| upd | updateStats() |

It is also possible (actually, required for some of them) to pass these methods arguments that are sent through to the corresponding Datadog method. For example, given the annotation declared above, in your app, you can:
```php
<?php
namespace App\Amazing\Service;
use App\Datadog\Stats;
class Foo
{
    /**
     * This is injected in the constructor
     * @var Stats
     **/
    protected $stats;
    ...
    public function bar()
    {
        $this->stats->incFooBarBaz(0.2, ['tagname' => 'value'], 5);
    }
    ...
}
```
Here, the metric `metric_prefix.foo.bar.baz` will be incremented, though with a sample rate of 0.2, also using the provided tag/value, and with an increment value of 5.

When using the methods, `timing()`, `microtiming()`, `gauge()`, `histogram()`, `distribution()`, and `set()`, it's necessary to provide an argument, as expected in the corresponding DogStatsd [methods](https://github.com/DataDog/php-datadogstatsd/blob/master/src/DogStatsd.php).

Using these simple rules, it's possible to make full use of the `increment()`, `decrement()`, `timing()`, `microtiming()`, `gauge()`, `histogram()`, `distribution()`, `set()` and `updateStats()` methods of the [DogStatsd](https://github.com/DataDog/php-datadogstatsd/blob/master/src/DogStatsd.php) class, simply by adding a `@method` annotation to your stats class, while also providing code completion in your app, and keeping PHPStan happy with your codebase.
