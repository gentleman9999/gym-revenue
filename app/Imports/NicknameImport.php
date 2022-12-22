<?php

declare(strict_types=1);

namespace App\Imports;

use App\Models\Nickname;
use Maatwebsite\Excel\Concerns\ToModel;

class NicknameImport implements ToModel
{
    public function model(array $row): void
    {
        $row = array_filter($row);
        $data = collect($row);

        foreach ($row as $name) {
            $nicknames = $data->filter(static fn ($value): bool => $value != $name);

            $nickname = Nickname::where('name', $name)->first();

            if (! $nickname) {
                $nickname = new Nickname();
                $nickname->name = $name;
            } else {
                $nicknames = collect(explode(',', $nickname->nicknames))
                                ->merge($nicknames)
                                ->unique();
            }

            $nickname->nicknames = $nicknames->join(',');
            $nickname->save();
        }
    }
}
