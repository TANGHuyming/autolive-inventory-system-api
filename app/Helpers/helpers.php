<?php

if (!function_exists('buildCacheKeyFromQuery')) {
    function buildCacheKeyFromQuery(string $name, array $query)
    {
        ksort($query);
        $filteredQuery = array_filter($query, function ($value) {
            return !is_null($value) && $value !== '';
        });
        $encodedString = json_encode($filteredQuery);
        return $name . '_' . md5($encodedString);
    }
}
