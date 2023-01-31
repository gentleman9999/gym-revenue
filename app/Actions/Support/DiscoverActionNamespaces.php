<?php

namespace App\Actions\Support;

use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class DiscoverActionNamespaces
{
    use asAction;

//    TODO: refactor using Lody
    public function handle(): array
    {
        $files = (new Finder())->files()->in(app()->path());

        return collect($files)
            ->reject(fn (SplFileInfo $file) => $file->getExtension() !== 'php')
            ->map(fn (SplFileInfo $file) => $this->fullQualifiedClassNameFromFile($file))
            ->filter(fn (string $class) => $this->checkIsAction($class))
            ->filter(fn (string $class) => (new \ReflectionClass($class))->isInstantiable())
            ->map(fn (string $class) => str($class)->explode('\\')->splice(0, -1)->implode('\\'))
            ->toArray();
    }

    private function checkIsAction(string $class)
    {
        $parentClasses = class_parents($class);
        $traits = class_uses($class);

        foreach ($parentClasses as $parentClass) {
            $traits = array_merge($traits, class_uses($parentClass));
        }

        foreach ($traits as $trait) {
            if ($trait == "Lorisleiva\Actions\Concerns\AsAction") {
                return true;
            }
        }

        return false;
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
