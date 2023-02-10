<?php

declare(strict_types=1);

namespace App\Imports;

use App\Models\Nickname;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;

class NicknameImport implements ToModel
{
    public function model(array $row): void
    {
        $row                = array_filter($row);
        $data               = new Collection($row);
        $existing_nicknames = Nickname::whereIn('name', $row)->get()->keyBy('name');

        foreach ($row as $name) {
            $nicknames = $data->filter(static fn ($value): bool => $value !== $name);
            $nickname  = $existing_nicknames[$name] ?? null;

            if ($nickname === null) {
                $nickname = new Nickname(['name' => $name]);
            } else {
                $nicknames = (new Collection(explode(',', $nickname->nicknames)))->merge($nicknames)->unique();
            }

            $nickname->nicknames = $nicknames->join(',');
            $nickname->save();
        }
    }
}
