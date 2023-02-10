<?php

declare(strict_types=1);

namespace App\Actions\Support;

use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class DiscoverDomainGraphql
{
    use asAction;

    public function handle(): \Illuminate\Support\Collection
    {
        $files = (new Finder())->files()->in(app()->path());

        return collect($files)
            ->reject(fn (SplFileInfo $file) => $file->getExtension() !== 'gql')
            ->reject(fn (SplFileInfo $file) => ! str($file->getFilename())->contains('schema'))
//            ->map(fn (SplFileInfo $file) => $this->fullQualifiedClassNameFromFile($file))
//            ->filter(fn (string $class) => $this->checkIsAction($class))
//            ->filter(fn (string $class) => (new \ReflectionClass($class))->isInstantiable())
            ->map(fn (string $class) => str($class)->explode('\\')->implode('\\'))
            ->values();
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
