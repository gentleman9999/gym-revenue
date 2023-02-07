<?php

namespace App\Actions\Support;

use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;

class PrepareLocalDevEnvironment
{
    use asAction;

    public string $commandSignature = 'prepare:local-dev-env';
    public string $commandDescription = 'Run scripts to prepare local dev environment';

    public function asCommand(Command $command)
    {
        $env = getenv('APP_ENV');
        //exit early if not local
        if ($env !== 'local') {
            $command->info('[PrepareLocalDevEnvironment] Exiting, not in local app environment');

            return;
        }
        $command->info('[PrepareLocalDevEnvironment] Calling lighthouse:ide-helper');
        $command->call('lighthouse:ide-helper');
        //TODO: run other IDE helpers or anything else that should only run in local environment
    }
}
