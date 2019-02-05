<?php

namespace ThePlankmeister\DatadogBundle;

use DataDog\DogStatsd;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Class AbstractDatadogService.
 */
abstract class AbstractDatadogService
{
    /**
     * @var DogStatsd
     */
    protected $statsd;

    /**
     * @var string
     */
    protected $prefix = '';

    /**
     * @var array
     */
    protected $methodMap = [];

    /**
     * @param DogStatsd             $statsd
     * @param ParameterBagInterface $params
     *
     * @throws \ReflectionException
     * @throws \UnexpectedValueException
     */
    public function __construct(DogStatsd $statsd, ParameterBagInterface $params)
    {
        $this->statsd = $statsd;
        $this->prefix = $params->get('datadog_metric_prefix');

        $this->createMetricMethodMap();
    }

    /**
     * Determines if the invoked method name should be considered available as a datadog stat, and if it is, invokes
     * the appropriate API call on that stat, with the arguments that may have been passed to the invoked method.
     *
     * @param string $name
     * @param array  $arguments
     *
     * @throws \BadMethodCallException
     */
    public function __call(string $name, array $arguments = [])
    {
        if (!array_key_exists($name, $this->methodMap)) {
            throw new \BadMethodCallException('Undefined dynamic method: '.$name);
        }

        array_unshift($arguments, $this->methodMap[$name]);
        switch (substr($name, 0, 3)) {
            case 'inc': $this->statsd->increment(...$arguments); break;
            case 'dec': $this->statsd->decrement(...$arguments); break;
            case 'tim':
                if (count($arguments) < 2) {
                    throw new \ArgumentCountError('Missing required timing argument.');
                }
                $this->statsd->timing(...$arguments);
                break;
            case 'mic':
                if (count($arguments) < 2) {
                    throw new \ArgumentCountError('Missing required timing argument.');
                }
                $this->statsd->microtiming(...$arguments);
                break;
        }
    }

    /**
     * This should be used in production on first deployment (or after adding new metrics) in order to force these
     * metrics to show up in Datadog, so they can be added to dashboards, etc.
     */
    public function initAllMetrics(): void
    {
        foreach (array_unique($this->methodMap) as $metricName) {
            $this->statsd->increment($metricName);
            $this->statsd->decrement($metricName);
        }
    }

    /**
     * Extracts the names of the methods declared in the class doc block, and uses those to determine the metric name
     * in Datadog, and creates a map of the method name to the stat name.
     *
     * @throws \ReflectionException
     * @throws \UnexpectedValueException
     */
    protected function createMetricMethodMap(): void
    {
        $ref = new \ReflectionClass($this);
        preg_match_all('~@method void (inc|dec|tim|mic)([a-zA-Z0-9_]+)\(.*\).*\n~', (string) $ref->getDocComment(), $matches);
        if (empty($matches[2])) {
            throw new \UnexpectedValueException(sprintf('No dynamic methods declared in class comment block for class %s', get_class($this)));
        }

        foreach ($matches[2] as $idx => $dynamicMethodName) {
            // Yes, I know... But sacrifices must be made upon the altar of PHPStan...
            $dotted = preg_replace(['/([a-z\d])([A-Z])/', '/([^\.])([A-Z][a-z])/'], '$1.$2', [$dynamicMethodName]);
            if (is_array($dotted)) {
                $dotted = array_shift($dotted);
            }
            $statName = strtolower($dotted);
            $this->methodMap[$matches[1][$idx].$dynamicMethodName] = sprintf('%s.%s', $this->prefix, $statName);
        }
    }
}
