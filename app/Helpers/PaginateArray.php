<?php

use Illuminate\Pagination\LengthAwarePaginator;

if (! function_exists('paginate_array')) {
    function paginate_array($request, $array, $per_page = 5): LengthAwarePaginator
    {
        if (! is_array($array)) {
            $array = $array->toArray();
        }
        $total = count($array);
        $current_page = $request->input("page") ?? 1;

        $starting_point = ($current_page * $per_page) - $per_page;

        $array = array_slice($array, $starting_point, $per_page, false);

        $array = new LengthAwarePaginator($array, $total, $per_page, $current_page, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        return $array;
    }
}
