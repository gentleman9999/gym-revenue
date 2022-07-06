<?php

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Request;

/**
 * Every function in here gets added globally.
 * Run 'composer dumpa' to see changes after you alter.
 */


if (! function_exists('paginate_array')) {
    function paginate_array(Request $request, array|Collection $array, int $per_page = 5): LengthAwarePaginator
    {
        if (! is_array($array)) {
            $array = $array->toArray();
        }
        $total = count($array);
        $current_page = $request->input("page") ?? 1;

        $starting_point = ($current_page * $per_page) - $per_page;

        $array = array_slice($array, $starting_point, $per_page, false);

        return new LengthAwarePaginator($array, $total, $per_page, $current_page, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);
    }
}
if (! function_exists('array_filter_only_keys')) {
    function array_filter_only_keys(array $array, array $allowedKeys = []): array
    {
        return array_filter(
            $array,
            function ($key) use ($allowedKeys) {
                return in_array($key, $allowedKeys);
            },
            ARRAY_FILTER_USE_KEY
        );
    }
}

if (! function_exists('array_filter_only_fillable')) {
    function array_filter_only_fillable(array $array, string $Model): array
    {
        $allowedKeys = (new $Model())->getFillable();

        return array_filter_only_keys($array, $allowedKeys);
    }
}
