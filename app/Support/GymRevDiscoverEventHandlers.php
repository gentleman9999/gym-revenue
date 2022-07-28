<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\EventSourcing\EventHandlers\EventHandler;
use Spatie\EventSourcing\Projectionist;
use Spatie\EventSourcing\Support\DiscoverEventHandlers;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

/**
 * The built-in auto-discover does not check if a projector/reactor is instantiable or not.
 */
class GymRevDiscoverEventHandlers extends DiscoverEventHandlers
{
    public function addToProjectionist(Projectionist $projectionist)
    {
        if (empty($this->directories)) {
            return;
        }

        $files = (new Finder())->files()->in($this->directories);

        return collect($files)
            ->reject(fn (SplFileInfo $file) => in_array($file->getPathname(), $this->ignoredFiles))
            ->map(fn (SplFileInfo $file) => $this->fullQualifiedClassNameFromFile($file))
            ->filter(fn (string $eventHandlerClass) => is_subclass_of($eventHandlerClass, EventHandler::class))
            //----- Start Custom Code for Auto Discovery
            ->filter(fn (string $eventHandlerClass) => (new \ReflectionClass($eventHandlerClass))->isInstantiable())
            //----- End Custom Code for Auto Discovery
            ->pipe(function (Collection $eventHandlers) use ($projectionist) {
                $projectionist->addEventHandlers($eventHandlers->toArray());
            });
    }

    private function fullQualifiedClassNameFromFile(SplFileInfo $file): string
    {
        $class = trim(Str::replaceFirst($this->basePath, '', $file->getRealPath()), DIRECTORY_SEPARATOR);

        $class = str_replace(
            [DIRECTORY_SEPARATOR, 'App\\'],
            ['\\', app()->getNamespace()],
            ucfirst(Str::replaceLast('.php', '', $class))
        );

        return $this->rootNamespace.$class;
    }
}
