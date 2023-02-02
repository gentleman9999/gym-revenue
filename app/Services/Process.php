<?php

declare(strict_types=1);

namespace App\Services;

use Amp\Parallel\Worker\DefaultPool;
use Amp\Parallel\Worker\Environment;
use Amp\Parallel\Worker\Pool;
use Amp\Parallel\Worker\Task;
use Amp\Parallel\Worker\TaskFailureError;
use Amp\Promise;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Log;
use Throwable;
use function Amp\call;
use function Amp\Promise\any;
use function Amp\Promise\wait;

/**
 * @method self queue(callable $callable, ...$args)
 * @method self nameQueue(string $name, callable $callable, ...$args)
 * @method self getQueue(string $name_or_index)
 * @method self onResolve(callable $callable)
 */
class Process implements Task
{
    /**
     * @param callable $callable Callable will be serialized.
     * @param mixed    $args Arguments to pass to the function. Must be serializable.
     */
    public function __construct(private $callable, private array $args = []) {}

    public function run(Environment $environment)
    {
        if (!$environment->exists(__CLASS__)) {
            $app = require_once __DIR__ . '/../../bootstrap/app.php';
            $app->make(Kernel::class)->bootstrap();
            $environment->set(__CLASS__, true);
        }

        return ($this->callable)(...$this->args);
    }

    /**
     * @return object|Process
     */
    public static function allocate(int $max_workers_count = Pool::DEFAULT_MAX_SIZE): object
    {
        return new class($max_workers_count) {
            /** @var Promise[] */
            private array $promises = [];
            private DefaultPool $pool;

            public function __construct(int $max_workers_count) {
                $this->pool = new DefaultPool($max_workers_count);
            }

            public function queue(callable $callable, ...$args): self
            {
                // NOTE: ALWAYS MAK SURE CALLBACK METHOD IS PUBLIC
                $this->promises[] = call(fn () => yield $this->pool->enqueue(new Process($callable, $args)));

                return $this;
            }

            public function nameQueue(string $name, callable $callable, ...$args): self
            {
                // NOTE: ALWAYS MAK SURE CALLBACK METHOD IS PUBLIC
                $this->promises[$name] = call(fn () => yield $this->pool->enqueue(new Process($callable, $args)));

                return $this;
            }

            public function onResolve(callable $callable): self
            {
                end($this->promises)->onResolve($callable);
                reset($this->promises);

                return $this;
            }

            public function getQueue(string $name_or_index): Promise
            {
                return $this->promises[$name_or_index];
            }

            public function run(Promise $promise = null, bool $fail_on_error = false): array
            {
                /** @var TaskFailureError[] $exceptions */
                if ($fail_on_error) {
                    [$exceptions, $values] = wait($promise ?: any($this->promises));
                } else {
                    $pools = $this->promises;
                    [$exceptions, $values] = wait(call(static fn () => any($pools)));
                }

                if (! empty($exceptions)) {
                    array_map(static function (Throwable $error) {
                        if ($error instanceof TaskFailureError) {
                            Log::error("{$error->getOriginalMessage()}\n{$error->getOriginalTraceAsString()}");
                        } else {
                            Log::error($error);
                        }

                    }, $exceptions);
                }

                return $values;
            }
        };
    }
}
