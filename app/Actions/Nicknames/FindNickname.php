<?php

declare(strict_types=1);

namespace App\Actions\Nicknames;

use App\Models\Nickname;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\Concerns\AsAction;

class FindNickname
{
    use AsAction;

    public function handle(string $name): Collection
    {
        $nicknames = new Collection();
        $nickname  = Nickname::where('name', $name)->first();

        if ($nickname) {
            $nicknames = new Collection(explode(',', $nickname->nicknames));
        }

        return $nicknames;
    }
}
