<?php

namespace spec\ThePlankmeister\DatadogBundle;

use DataDog\DogStatsd;
use ThePlankmeister\DatadogBundle\AbstractDatadogService;
use Prophecy\Prophet;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class DataDogServiceSpec extends \PhpSpec\ObjectBehavior
{
    /**
     * @var Prophet
     */
    private $prophet;

    public function let(
        DogStatsd $stats,
        ParameterBagInterface $params
    ) {
        $this->prophet = new Prophet();
        $params->get('datadog_metric_prefix')->willReturn('custom_prefix');
        $this->beAnInstanceOf(TestClass1::class);
        $this->beConstructedWith($stats, $params);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(TestClass1::class);
    }

    public function it_throws_exception_when_subclass_has_no_declared_methods(DogStatsd $stats, ParameterBagInterface $params)
    {
        $params->get('datadog_metric_prefix')->willReturn('custom_prefix');
        $this->beAnInstanceOf(TestClass2::class);
        $this->beConstructedWith($stats, $params);
        $this->shouldThrow(new \UnexpectedValueException('No dynamic methods declared in class comment block for class '.TestClass2::class))->duringInstantiation();
    }

    public function it_throws_exception_with_undeclared_methods()
    {
        $this->shouldThrow(new \BadMethodCallException('Undefined dynamic method: timUndeclaredMethod'))->during('timUndeclaredMethod');
        $this->shouldThrow(new \BadMethodCallException('Undefined dynamic method: micUndeclaredMethod'))->during('micUndeclaredMethod');
        $this->shouldThrow(new \BadMethodCallException('Undefined dynamic method: gauUndeclaredMethod'))->during('gauUndeclaredMethod');
        $this->shouldThrow(new \BadMethodCallException('Undefined dynamic method: hisUndeclaredMethod'))->during('hisUndeclaredMethod');
        $this->shouldThrow(new \BadMethodCallException('Undefined dynamic method: disUndeclaredMethod'))->during('disUndeclaredMethod');
        $this->shouldThrow(new \BadMethodCallException('Undefined dynamic method: setUndeclaredMethod'))->during('setUndeclaredMethod');
        $this->shouldThrow(new \BadMethodCallException('Undefined dynamic method: incUndeclaredMethod'))->during('incUndeclaredMethod');
        $this->shouldThrow(new \BadMethodCallException('Undefined dynamic method: decUndeclaredMethod'))->during('decUndeclaredMethod');
        $this->shouldThrow(new \BadMethodCallException('Undefined dynamic method: updUndeclaredMethod'))->during('updUndeclaredMethod');
    }

    public function it_behaves_correctly_with_timing_methods(DogStatsd $stats)
    {
        $this->shouldThrow(new \ArgumentCountError('spec\ThePlankmeister\DatadogBundle\TestClass1::timAuthDecode() requires $time argument.'))->during('timAuthDecode');

        $stats->timing('custom_prefix.auth.decode', 12345)->shouldBeCalledOnce();
        $this->timAuthDecode(12345);

        $stats->timing('custom_prefix.auth.decode', 12345, 5.2)->shouldBeCalledOnce();
        $this->timAuthDecode(12345, 5.2);

        $stats->timing('custom_prefix.auth.decode', 12345, 0.75, ['tag' => 'value'])->shouldBeCalledOnce();
        $this->timAuthDecode(12345, 0.75, ['tag' => 'value']);

        $stats->timing('custom_prefix.auth.decode', 12345, 0.75, ['tag' => 'value'], 'an extraneous argument that still works')->shouldBeCalledOnce();
        $this->timAuthDecode(12345, 0.75, ['tag' => 'value'], 'an extraneous argument that still works');
    }

    public function it_behaves_correctly_with_microtiming_methods(DogStatsd $stats)
    {
        $this->shouldThrow(new \ArgumentCountError('spec\ThePlankmeister\DatadogBundle\TestClass1::micAuthEncode() requires $time argument.'))->during('micAuthEncode');

        $stats->microtiming('custom_prefix.auth.encode', 12345)->shouldBeCalledOnce();
        $this->micAuthEncode(12345);

        $stats->microtiming('custom_prefix.auth.encode', 12345, 5.2)->shouldBeCalledOnce();
        $this->micAuthEncode(12345, 5.2);

        $stats->microtiming('custom_prefix.auth.encode', 12345, 0.75, ['tag' => 'value'])->shouldBeCalledOnce();
        $this->micAuthEncode(12345, 0.75, ['tag' => 'value']);

        $stats->microtiming('custom_prefix.auth.encode', 12345, 0.75, ['tag' => 'value'], 'an extraneous argument that still works')->shouldBeCalledOnce();
        $this->micAuthEncode(12345, 0.75, ['tag' => 'value'], 'an extraneous argument that still works');
    }

    public function it_behaves_correctly_with_gauge_methods(DogStatsd $stats)
    {
        $this->shouldThrow(new \ArgumentCountError('spec\ThePlankmeister\DatadogBundle\TestClass1::gauFizzBuzz() requires $value argument.'))->during('gauFizzBuzz');

        $stats->gauge('custom_prefix.fizz.buzz', 12345)->shouldBeCalledOnce();
        $this->gauFizzBuzz(12345);

        $stats->gauge('custom_prefix.fizz.buzz', 12345, 5.2)->shouldBeCalledOnce();
        $this->gauFizzBuzz(12345, 5.2);

        $stats->gauge('custom_prefix.fizz.buzz', 12345, 0.75, ['tag' => 'value'])->shouldBeCalledOnce();
        $this->gauFizzBuzz(12345, 0.75, ['tag' => 'value']);

        $stats->gauge('custom_prefix.fizz.buzz', 12345, 0.75, ['tag' => 'value'], 'an extraneous argument that still works')->shouldBeCalledOnce();
        $this->gauFizzBuzz(12345, 0.75, ['tag' => 'value'], 'an extraneous argument that still works');
    }

    public function it_behaves_correctly_with_histogram_methods(DogStatsd $stats)
    {
        $this->shouldThrow(new \ArgumentCountError('spec\ThePlankmeister\DatadogBundle\TestClass1::hisFooBar() requires $value argument.'))->during('hisFooBar');

        $stats->histogram('custom_prefix.foo.bar', 12345)->shouldBeCalledOnce();
        $this->hisFooBar(12345);

        $stats->histogram('custom_prefix.foo.bar', 12345, 5.2)->shouldBeCalledOnce();
        $this->hisFooBar(12345, 5.2);

        $stats->histogram('custom_prefix.foo.bar', 12345, 0.75, ['tag' => 'value'])->shouldBeCalledOnce();
        $this->hisFooBar(12345, 0.75, ['tag' => 'value']);

        $stats->histogram('custom_prefix.foo.bar', 12345, 0.75, ['tag' => 'value'], 'an extraneous argument that still works')->shouldBeCalledOnce();
        $this->hisFooBar(12345, 0.75, ['tag' => 'value'], 'an extraneous argument that still works');
    }

    public function it_behaves_correctly_with_distribution_methods(DogStatsd $stats)
    {
        $this->shouldThrow(new \ArgumentCountError('spec\ThePlankmeister\DatadogBundle\TestClass1::disBooFar() requires $value argument.'))->during('disBooFar');

        $stats->distribution('custom_prefix.boo.far', 12345)->shouldBeCalledOnce();
        $this->disBooFar(12345);

        $stats->distribution('custom_prefix.boo.far', 12345, 5.2)->shouldBeCalledOnce();
        $this->disBooFar(12345, 5.2);

        $stats->distribution('custom_prefix.boo.far', 12345, 0.75, ['tag' => 'value'])->shouldBeCalledOnce();
        $this->disBooFar(12345, 0.75, ['tag' => 'value']);

        $stats->distribution('custom_prefix.boo.far', 12345, 0.75, ['tag' => 'value'], 'an extraneous argument that still works')->shouldBeCalledOnce();
        $this->disBooFar(12345, 0.75, ['tag' => 'value'], 'an extraneous argument that still works');
    }

    public function it_behaves_correctly_with_set_methods(DogStatsd $stats)
    {
        $this->shouldThrow(new \ArgumentCountError('spec\ThePlankmeister\DatadogBundle\TestClass1::setFozzBazz() requires $value argument.'))->during('setFozzBazz');

        $stats->set('custom_prefix.fozz.bazz', 12345)->shouldBeCalledOnce();
        $this->setFozzBazz(12345);

        $stats->set('custom_prefix.fozz.bazz', 12345, 5.2)->shouldBeCalledOnce();
        $this->setFozzBazz(12345, 5.2);

        $stats->set('custom_prefix.fozz.bazz', 12345, 0.75, ['tag' => 'value'])->shouldBeCalledOnce();
        $this->setFozzBazz(12345, 0.75, ['tag' => 'value']);

        $stats->set('custom_prefix.fozz.bazz', 12345, 0.75, ['tag' => 'value'], 'an extraneous argument that still works')->shouldBeCalledOnce();
        $this->setFozzBazz(12345, 0.75, ['tag' => 'value'], 'an extraneous argument that still works');
    }

    public function it_behaves_correctly_with_increment_methods(DogStatsd $stats)
    {
        $stats->increment('custom_prefix.sid.jim.bob')->shouldBeCalledOnce();
        $this->incSidJimBob();

        $stats->increment('custom_prefix.sid.jim.bob', 0.5)->shouldBeCalledOnce();
        $this->incSidJimBob(0.5);

        $stats->increment('custom_prefix.sid.jim.bob', 0.5, ['tagname' => 'value'])->shouldBeCalledOnce();
        $this->incSidJimBob(0.5, ['tagname' => 'value']);

        $stats->increment('custom_prefix.sid.jim.bob', 0.5, ['tagname' => 'value'], 5)->shouldBeCalledOnce();
        $this->incSidJimBob(0.5, ['tagname' => 'value'], 5);
    }

    public function it_behaves_correctly_with_decrement_methods(DogStatsd $stats)
    {
        $stats->decrement('custom_prefix.ant.dec.tim')->shouldBeCalledOnce();
        $this->decAntDecTim();

        $stats->decrement('custom_prefix.ant.dec.tim', 0.5)->shouldBeCalledOnce();
        $this->decAntDecTim(0.5);

        $stats->decrement('custom_prefix.ant.dec.tim', 0.5, ['tagname' => 'value'])->shouldBeCalledOnce();
        $this->decAntDecTim(0.5, ['tagname' => 'value']);

        $stats->decrement('custom_prefix.ant.dec.tim', 0.5, ['tagname' => 'value'], 5)->shouldBeCalledOnce();
        $this->decAntDecTim(0.5, ['tagname' => 'value'], 5);
    }

    public function it_behaves_correctly_with_updateStats_methods(DogStatsd $stats)
    {
        $stats->updateStats('custom_prefix.spurious.floating.widgets')->shouldBeCalledOnce();
        $this->updSpuriousFloatingWidgets();

        $stats->updateStats('custom_prefix.spurious.floating.widgets', 12345)->shouldBeCalledOnce();
        $this->updSpuriousFloatingWidgets(12345);

        $stats->updateStats('custom_prefix.spurious.floating.widgets', 12345, 5.2)->shouldBeCalledOnce();
        $this->updSpuriousFloatingWidgets(12345, 5.2);

        $stats->updateStats('custom_prefix.spurious.floating.widgets', 12345, 0.75, ['tag' => 'value'])->shouldBeCalledOnce();
        $this->updSpuriousFloatingWidgets(12345, 0.75, ['tag' => 'value']);

        $stats->updateStats('custom_prefix.spurious.floating.widgets', 12345, 0.75, ['tag' => 'value'], 'an extraneous argument that still works')->shouldBeCalledOnce();
        $this->updSpuriousFloatingWidgets(12345, 0.75, ['tag' => 'value'], 'an extraneous argument that still works');
    }

    public function it_inits_all_metrics(DogStatsd $stats)
    {
        $stats->increment('custom_prefix.auth.decode')->shouldBeCalledOnce();
        $stats->decrement('custom_prefix.auth.decode')->shouldBeCalledOnce();
        $stats->increment('custom_prefix.auth.encode')->shouldBeCalledOnce();
        $stats->decrement('custom_prefix.auth.encode')->shouldBeCalledOnce();
        $stats->increment('custom_prefix.fizz.buzz')->shouldBeCalledOnce();
        $stats->decrement('custom_prefix.fizz.buzz')->shouldBeCalledOnce();
        $stats->increment('custom_prefix.foo.bar')->shouldBeCalledOnce();
        $stats->decrement('custom_prefix.foo.bar')->shouldBeCalledOnce();
        $stats->increment('custom_prefix.boo.far')->shouldBeCalledOnce();
        $stats->decrement('custom_prefix.boo.far')->shouldBeCalledOnce();
        $stats->increment('custom_prefix.fozz.bazz')->shouldBeCalledOnce();
        $stats->decrement('custom_prefix.fozz.bazz')->shouldBeCalledOnce();
        $stats->increment('custom_prefix.sid.jim.bob')->shouldBeCalledOnce();
        $stats->decrement('custom_prefix.sid.jim.bob')->shouldBeCalledOnce();
        $stats->increment('custom_prefix.ant.dec.tim')->shouldBeCalledOnce();
        $stats->decrement('custom_prefix.ant.dec.tim')->shouldBeCalledOnce();
        $stats->increment('custom_prefix.spurious.floating.widgets')->shouldBeCalledOnce();
        $stats->decrement('custom_prefix.spurious.floating.widgets')->shouldBeCalledOnce();

        $this->initAllMetrics();
    }
}

/**
 * @method void timAuthDecode(float $time, float $sampleRate = 1.0, array|string|null $tags = null) How long it takes to decode authentication token
 * @method void micAuthEncode(float $time, float $sampleRate = 1.0, array|string|null $tags = null) How long it takes to encode a new authentication token
 * @method void gauFizzBuzz(float $value, float $sampleRate = 1.0, array|string|null $tags = null) A description of what this method does to the fizz.buzz gauge
 * @method void hisFooBar(float $value, float $sampleRate = 1.0, array|string|null $tags = null) A description of what this method does to the foo.bar histogram
 * @method void disBooFar(float $value, float $sampleRate = 1.0, array|string|null $tags = null) A description of what this method does to the boo.far distribution
 * @method void setFozzBazz(float $value, float $sampleRate = 1.0, array|string|null $tags = null) A description of why this method adds/subtracts an arbitrary value to the fozz.bazz metric
 * @method void incSidJimBob(float $sampleRate = 1.0, array|string|null $tags = null, $incValue = 1) Increment the sid.jim.bob metric
 * @method void decAntDecTim(float $sampleRate = 1.0, array|string|null $tags = null, $decValue = 1) Decrement the ant.dec.tim metric
 * @method void updSpuriousFloatingWidgets(int $delta, float $sampleRate = 1.0, array|string|null $tags = null) Updates the spurious.floating.widgets metric by $delta
 * @method void flowStart() When a user commences a traversal through the flow. This will not appear in the available Datadog metrics because its prefix is invalid.
 */
class TestClass1 extends AbstractDatadogService
{
}

/**
 * No methods defined here...
 */
class TestClass2 extends AbstractDatadogService
{
}
