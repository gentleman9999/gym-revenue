<?php
// phpcs:ignoreFile
declare(strict_types=1);

namespace App\Domain\Draftable\Mutations;

use Illuminate\Container\Container;
use Illuminate\Routing\Route;
use Illuminate\Routing\RouteDependencyResolverTrait;
use Illuminate\Support\Str;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\DecorateActions;
use Lorisleiva\Actions\Concerns\WithAttributes;

class ControllerDecoratorOverride
{
    use RouteDependencyResolverTrait;
    use DecorateActions;

    protected Container $container;

    protected Route $route;

    /** @var array<string> */
    protected array $middleware = [];

    protected bool $executedAtLeastOne = false;

    public function __construct($action, Route $route)
    {
        $this->container = Container::getInstance();
        $this->route     = $route;
        $this->setAction($action);
        $this->replaceRouteMethod();

        if ($this->hasMethod('getControllerMiddleware')) {
            $this->middleware = $this->resolveAndCallMethod('getControllerMiddleware');
        }
    }

    public function getRoute(): Route
    {
        return $this->route;
    }

    /**
     * @return array<string, mixed>
     */
    public function getMiddleware(): array
    {
        return array_map(fn($middleware): array => ['middleware' => $middleware, 'options' => []], $this->middleware);
    }

    public function callAction(string $method): mixed
    {
        return $this($method);
    }

    protected function refreshAction(): void
    {
        if ($this->executedAtLeastOne) {
            $this->setAction(app(get_class($this->action)));
        }

        $this->executedAtLeastOne = true;
    }

    protected function refreshRequest(): ActionRequest
    {

        app()->forgetInstance(ActionRequest::class);

        /** @var ActionRequest $request */
        $request = app(ActionRequest::class);
        $request->setAction($this->action);
        app()->instance(ActionRequest::class, $request);

        return $request;
    }

    protected function replaceRouteMethod(): void
    {
        if (! isset($this->route->action['uses'])) {
            return;
        }

        $current_method = Str::afterLast($this->route->action['uses'], '@');
        $new_method     = $this->getDefaultRouteMethod();

        if ($current_method !== '__invoke' || $current_method === $new_method) {
            return;
        }

        $this->route->action['uses'] = (string) Str::of($this->route->action['uses'])
            ->beforeLast('@')
            ->append('@' . $new_method);
    }

    protected function getDefaultRouteMethod(): string
    {
        $control_method = in_array(request()->query('is_draft'), ['1', 'true']) ? 'handleDraft' : 'asController';
        if ($this->hasMethod($control_method)) {
            return $control_method;
        }

        return $this->hasMethod('handle') ? 'handle' : '__invoke';
    }

    protected function isExplicitMethod(string $method): bool
    {
        return ! in_array($method, ['asController', 'handleDraft', 'handle', '__invoke']);
    }

    protected function run(string $method): mixed
    {
        if ($this->hasMethod($method)) {
            return $this->resolveFromRouteAndCall($method);
        }

        return null;
    }

    protected function shouldValidateRequest(string $method): bool
    {
        return $this->hasAnyValidationMethod()
            && ! $this->isExplicitMethod($method)
            && ! $this->hasTrait(WithAttributes::class);
    }

    protected function hasAnyValidationMethod(): bool
    {
        return $this->hasMethod('authorize')
            || $this->hasMethod('rules')
            || $this->hasMethod('withValidator')
            || $this->hasMethod('afterValidator')
            || $this->hasMethod('getValidator');
    }

    protected function resolveFromRouteAndCall(string $method): mixed
    {
        $this->container = Container::getInstance();
        $arguments       = $this->resolveClassMethodDependencies(
            $this->route->parametersWithoutNulls(),
            $this->action,
            $method,
        );

        if ($method === 'asController' && $this->hasMethod('beforeAsController')) {
            $this->action->beforeAsController($this->container->get(ActionRequest::class));
        }

        return $this->action->{$method}(...array_values($arguments));
    }

    public function __invoke(string $method): mixed
    {
        $this->refreshAction();
        $request = $this->refreshRequest();

        if ($this->shouldValidateRequest($method)) {
            $request->validate();
        }

        $response = $this->run($method);

        if ($this->hasMethod('jsonResponse') && $request->expectsJson()) {
            $response = $this->callMethod('jsonResponse', [$response, $request]);
        } elseif ($this->hasMethod('htmlResponse') && ! $request->expectsJson()) {
            $response = $this->callMethod('htmlResponse', [$response, $request]);
        }

        return $response;
    }
}
