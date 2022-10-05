<?php

namespace App\Actions\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class DiscoverModelNamespaces
{
    use asAction;

    public function handle(): array
    {
        $files = (new Finder())->files()->in(app()->path());

        return collect($files)
//            ->reject(fn (SplFileInfo $file) => in_array($file->getPathname(), $this->ignoredFiles))
            ->map(fn (SplFileInfo $file) => $this->fullQualifiedClassNameFromFile($file))
            ->filter(fn (string $class) => is_subclass_of($class, Model::class))
            ->filter(fn (string $class) => (new \ReflectionClass($class))->isInstantiable())
            ->map(fn (string $class) => str($class)->explode('\\')->splice(0, -1)->implode('\\'))
            ->toArray();
    }

    private function fullQualifiedClassNameFromFile(SplFileInfo $file): string
    {
        $class = trim(Str::replaceFirst(base_path(), '', $file->getRealPath()), DIRECTORY_SEPARATOR);

        return str_replace(
            [DIRECTORY_SEPARATOR, 'App\\'],
            ['\\', app()->getNamespace()],
            ucfirst(Str::replaceLast('.php', '', $class))
        );
    }
}
