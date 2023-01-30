<?php

namespace App\Actions\Support;

use Lorisleiva\Actions\Concerns\AsAction;
use Nuwave\Lighthouse\Console\IdeHelperCommand;

class PrepareLocalDevEnvironment
{
    use asAction;

    public string $commandSignature = 'prepare:local-dev-env';
    public string $commandDescription = 'Run scripts to prepare local dev environment';


    public function asCommand($command)
    {
        $env = getenv('APP_ENV');
        //exit early if not local
        if($env !== 'local') {
            $command->getOutput()->writeln('[PrepareLocalDevEnvironment] Exiting, not in local app environment');
            return;
        }
        $command->getOutput()->writeln('[PrepareLocalDevEnvironment] Calling lighthouse:ide-helper');
        $command->call('lighthouse:ide-helper');
        //TODO: run other IDE helpers or anything else that should only run in local environment
    }
}
