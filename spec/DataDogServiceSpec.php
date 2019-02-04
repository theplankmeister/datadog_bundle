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
        $this->shouldThrow(new \BadMethodCallException('Undefined dynamic method: decUndeclaredMethod'))->during('decUndeclaredMethod');
        $this->shouldThrow(new \BadMethodCallException('Undefined dynamic method: incUndeclaredMethod'))->during('incUndeclaredMethod');
        $this->shouldThrow(new \BadMethodCallException('Undefined dynamic method: timUndeclaredMethod'))->during('timUndeclaredMethod');
        $this->shouldThrow(new \BadMethodCallException('Undefined dynamic method: micUndeclaredMethod'))->during('micUndeclaredMethod');
        $this->shouldThrow(new \BadMethodCallException('Undefined dynamic method: methodNotDeclared'))->during('methodNotDeclared');
    }

    public function it_invokes_declared_increment_methods_with_correct_arguments(DogStatsd $stats)
    {
        $stats->increment('custom_prefix.netaxept.registration_failed')->shouldBeCalledOnce();
        $this->incNetaxeptRegistration_failed();

        $stats->increment('custom_prefix.netaxept.registration_failed', 0.5)->shouldBeCalledOnce();
        $this->incNetaxeptRegistration_failed(0.5);

        $stats->increment('custom_prefix.netaxept.registration_failed', 0.5, ['tagname' => 'value'])->shouldBeCalledOnce();
        $this->incNetaxeptRegistration_failed(0.5, ['tagname' => 'value']);

        $stats->increment('custom_prefix.netaxept.registration_failed', 0.5, ['tagname' => 'value'], 5)->shouldBeCalledOnce();
        $this->incNetaxeptRegistration_failed(0.5, ['tagname' => 'value'], 5);
    }

    public function it_invokes_declared_decrement_methods_with_correct_arguments(DogStatsd $stats)
    {
        $stats->decrement('custom_prefix.authorisation.missing.session')->shouldBeCalledOnce();
        $this->decAuthorisationMissingSession();

        $stats->decrement('custom_prefix.authorisation.missing.session', 0.5)->shouldBeCalledOnce();
        $this->decAuthorisationMissingSession(0.5);

        $stats->decrement('custom_prefix.authorisation.missing.session', 0.5, ['tagname' => 'value'])->shouldBeCalledOnce();
        $this->decAuthorisationMissingSession(0.5, ['tagname' => 'value']);

        $stats->decrement('custom_prefix.authorisation.missing.session', 0.5, ['tagname' => 'value'], 5)->shouldBeCalledOnce();
        $this->decAuthorisationMissingSession(0.5, ['tagname' => 'value'], 5);
    }

    public function it_invokes_declared_timing_methods_with_correct_arguments(DogStatsd $stats)
    {
        $this->shouldThrow(new \ArgumentCountError('Missing required timing argument.'))->during('timSubscribeFailed');

        $stats->timing('custom_prefix.subscribe.failed', 12345)->shouldBeCalledOnce();
        $this->timSubscribeFailed(12345);

        $stats->timing('custom_prefix.subscribe.failed', 12345, 0.5)->shouldBeCalledOnce();
        $this->timSubscribeFailed(12345, 0.5);

        $stats->timing('custom_prefix.subscribe.failed', 12345, 0.5, ['tagname' => 'value'])->shouldBeCalledOnce();
        $this->timSubscribeFailed(12345, 0.5, ['tagname' => 'value']);
    }

    public function it_invokes_declared_microtiming_methods_with_correct_arguments(DogStatsd $stats)
    {
        $this->shouldThrow(new \ArgumentCountError('Missing required timing argument.'))->during('micSubscribeFailed');

        $stats->microtiming('custom_prefix.subscribe.failed', 12345)->shouldBeCalledOnce();
        $this->micSubscribeFailed(12345);

        $stats->microtiming('custom_prefix.subscribe.failed', 12345, 0.5)->shouldBeCalledOnce();
        $this->micSubscribeFailed(12345, 0.5);

        $stats->microtiming('custom_prefix.subscribe.failed', 12345, 0.5, ['tagname' => 'value'])->shouldBeCalledOnce();
        $this->micSubscribeFailed(12345, 0.5, ['tagname' => 'value']);
    }
}

/**
 * @method void incNetaxeptRegistration_failed(float $sampleRate = 1.0, array|string|null $tags = null, $incValue = 1) When Netaxept registration fails
 * @method void decAuthorisationMissingSession(float $sampleRate = 1.0, array|string|null $tags = null, $incValue = 1) When authorisation is missing a session
 * @method void timSubscribeFailed(float $time, float $sampleRate = 1.0, array|string|null $tags = null)               When email subscription fails
 * @method void micSubscribeFailed(float $time, float $sampleRate = 1.0, array|string|null $tags = null)               When email subscription fails
 * @method void flowStart()                                                                                            When a user commences a traversal through the flow
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
