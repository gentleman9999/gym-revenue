<?php

declare(strict_types=1);

namespace App\Providers;

use App\Support\GymRevDiscoverEventHandlers;
use Illuminate\Support\ServiceProvider;
use Spatie\EventSourcing\Projectionist;
use Spatie\EventSourcing\Support\Composer;

class EventSourcingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // you can also add multiple projectors in one go
//        Projectionist::addProjectors([
//            AnotherProjector::class,
//            YetAnotherProjector::class,
//        ]);
        $this->discoverEventHandlers();
    }

    protected function discoverEventHandlers(): void
    {
        $projectionist = app(Projectionist::class);

        $cachedEventHandlers = $this->getCachedEventHandlers();

        if ($cachedEventHandlers !== null) {
            $projectionist->addEventHandlers($cachedEventHandlers);

            return;
        }

        $temp = (new GymRevDiscoverEventHandlers())
            ->within([app()->path()])
            ->useBasePath(config('event-sourcing.auto_discover_base_path', base_path()))
            ->ignoringFiles(Composer::getAutoloadedFiles(base_path('composer.json')))
            ->addToProjectionist($projectionist);
//        dd($temp);
    }

    protected function getCachedEventHandlers(): ?array
    {
        $cachedEventHandlersPath = config('event-sourcing.cache_path') . '/event-handlers.php';

        if (! file_exists($cachedEventHandlersPath)) {
            return null;
        }

        return require $cachedEventHandlersPath;
    }
}
