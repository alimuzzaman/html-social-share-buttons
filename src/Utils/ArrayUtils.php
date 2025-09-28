<?php
namespace HtmlSocialShare\Utils;

/**
 * Pure array and configuration processing functions
 * 
 * This class contains only pure functions for processing arrays,
 * configurations, and data transformations without side effects.
 * 
 * @since 3.0.0
 */
class ArrayUtils
{
    /**
     * Deep merge arrays with priority to right array
     * 
     * @param array $left Left array (lower priority)
     * @param array $right Right array (higher priority)
     * @return array Merged array
     */
    public static function deepMerge(array $left, array $right): array
    {
        $merged = $left;

        foreach ($right as $key => $value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = self::deepMerge($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }

    /**
     * Get nested array value with dot notation
     * 
     * @param array $array Array to search
     * @param string $key Dot notation key (e.g., 'settings.networks.facebook')
     * @param mixed $default Default value if not found
     * @return mixed Found value or default
     */
    public static function get(array $array, string $key, $default = null)
    {
        if (empty($key)) {
            return $array;
        }

        if (isset($array[$key])) {
            return $array[$key];
        }

        $keys = explode('.', $key);
        $value = $array;

        foreach ($keys as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    /**
     * Set nested array value with dot notation
     * 
     * @param array $array Array to modify
     * @param string $key Dot notation key
     * @param mixed $value Value to set
     * @return array Modified array
     */
    public static function set(array $array, string $key, $value): array
    {
        if (empty($key)) {
            return $array;
        }

        $keys = explode('.', $key);
        $current = &$array;

        foreach ($keys as $segment) {
            if (!isset($current[$segment]) || !is_array($current[$segment])) {
                $current[$segment] = [];
            }
            $current = &$current[$segment];
        }

        $current = $value;
        return $array;
    }

    /**
     * Remove nested array value with dot notation
     * 
     * @param array $array Array to modify
     * @param string $key Dot notation key
     * @return array Modified array
     */
    public static function unset(array $array, string $key): array
    {
        if (empty($key)) {
            return $array;
        }

        $keys = explode('.', $key);
        $lastKey = array_pop($keys);
        $current = &$array;

        foreach ($keys as $segment) {
            if (!isset($current[$segment]) || !is_array($current[$segment])) {
                return $array; // Path doesn't exist
            }
            $current = &$current[$segment];
        }

        unset($current[$lastKey]);
        return $array;
    }

    /**
     * Check if nested array key exists with dot notation
     * 
     * @param array $array Array to search
     * @param string $key Dot notation key
     * @return bool True if key exists
     */
    public static function has(array $array, string $key): bool
    {
        if (empty($key)) {
            return false;
        }

        if (array_key_exists($key, $array)) {
            return true;
        }

        $keys = explode('.', $key);
        $current = $array;

        foreach ($keys as $segment) {
            if (!is_array($current) || !array_key_exists($segment, $current)) {
                return false;
            }
            $current = $current[$segment];
        }

        return true;
    }

    /**
     * Filter array by callable predicate
     * 
     * @param array $array Array to filter
     * @param callable $predicate Predicate function
     * @param bool $preserveKeys Whether to preserve array keys
     * @return array Filtered array
     */
    public static function filter(array $array, callable $predicate, bool $preserveKeys = true): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            if ($predicate($value, $key)) {
                if ($preserveKeys) {
                    $result[$key] = $value;
                } else {
                    $result[] = $value;
                }
            }
        }

        return $result;
    }

    /**
     * Map array values through callable function
     * 
     * @param array $array Array to map
     * @param callable $mapper Mapper function
     * @param bool $preserveKeys Whether to preserve array keys
     * @return array Mapped array
     */
    public static function map(array $array, callable $mapper, bool $preserveKeys = true): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            $mapped = $mapper($value, $key);
            
            if ($preserveKeys) {
                $result[$key] = $mapped;
            } else {
                $result[] = $mapped;
            }
        }

        return $result;
    }

    /**
     * Reduce array to single value
     * 
     * @param array $array Array to reduce
     * @param callable $reducer Reducer function
     * @param mixed $initial Initial value
     * @return mixed Reduced value
     */
    public static function reduce(array $array, callable $reducer, $initial = null)
    {
        $accumulator = $initial;

        foreach ($array as $key => $value) {
            $accumulator = $reducer($accumulator, $value, $key);
        }

        return $accumulator;
    }

    /**
     * Group array elements by key or callable
     * 
     * @param array $array Array to group
     * @param string|callable $groupBy Key name or callable function
     * @return array Grouped array
     */
    public static function groupBy(array $array, $groupBy): array
    {
        $result = [];

        foreach ($array as $item) {
            if (is_callable($groupBy)) {
                $key = $groupBy($item);
            } elseif (is_string($groupBy) && is_array($item)) {
                $key = $item[$groupBy] ?? 'null';
            } else {
                $key = 'null';
            }

            $result[$key][] = $item;
        }

        return $result;
    }

    /**
     * Sort array by key or callable
     * 
     * @param array $array Array to sort
     * @param string|callable $sortBy Key name or callable function
     * @param string $direction 'asc' or 'desc'
     * @return array Sorted array
     */
    public static function sortBy(array $array, $sortBy, string $direction = 'asc'): array
    {
        $sorted = $array;

        usort($sorted, function ($a, $b) use ($sortBy, $direction) {
            if (is_callable($sortBy)) {
                $aValue = $sortBy($a);
                $bValue = $sortBy($b);
            } elseif (is_string($sortBy)) {
                $aValue = is_array($a) ? ($a[$sortBy] ?? null) : null;
                $bValue = is_array($b) ? ($b[$sortBy] ?? null) : null;
            } else {
                $aValue = $a;
                $bValue = $b;
            }

            $comparison = $aValue <=> $bValue;
            
            return $direction === 'desc' ? -$comparison : $comparison;
        });

        return $sorted;
    }

    /**
     * Pluck values from array of arrays/objects
     * 
     * @param array $array Array to pluck from
     * @param string $key Key to pluck
     * @param string|null $indexBy Key to use as array index
     * @return array Plucked values
     */
    public static function pluck(array $array, string $key, ?string $indexBy = null): array
    {
        $result = [];

        foreach ($array as $item) {
            $value = null;
            $index = null;

            if (is_array($item)) {
                $value = $item[$key] ?? null;
                $index = $indexBy ? ($item[$indexBy] ?? null) : null;
            } elseif (is_object($item)) {
                $value = $item->$key ?? null;
                $index = $indexBy ? ($item->$indexBy ?? null) : null;
            }

            if ($value !== null) {
                if ($indexBy && $index !== null) {
                    $result[$index] = $value;
                } else {
                    $result[] = $value;
                }
            }
        }

        return $result;
    }

    /**
     * Flatten multi-dimensional array
     * 
     * @param array $array Array to flatten
     * @param int $depth Maximum depth to flatten (0 = unlimited)
     * @return array Flattened array
     */
    public static function flatten(array $array, int $depth = 0): array
    {
        $result = [];

        foreach ($array as $value) {
            if (is_array($value) && ($depth === 0 || $depth > 1)) {
                $flattened = self::flatten($value, $depth > 0 ? $depth - 1 : 0);
                $result = array_merge($result, $flattened);
            } else {
                $result[] = $value;
            }
        }

        return $result;
    }

    /**
     * Remove empty values from array (recursive)
     * 
     * @param array $array Array to clean
     * @param bool $recursive Whether to clean recursively
     * @return array Cleaned array
     */
    public static function removeEmpty(array $array, bool $recursive = false): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                if ($recursive) {
                    $cleaned = self::removeEmpty($value, true);
                    if (!empty($cleaned)) {
                        $result[$key] = $cleaned;
                    }
                } elseif (!empty($value)) {
                    $result[$key] = $value;
                }
            } elseif (!self::isEmpty($value)) {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Check if value is considered empty
     * 
     * @param mixed $value Value to check
     * @return bool True if empty
     */
    public static function isEmpty($value): bool
    {
        return $value === null || 
               $value === '' || 
               $value === [] || 
               ($value === 0 && !is_bool($value)) ||
               ($value === '0');
    }

    /**
     * Get array intersection by key
     * 
     * @param array $array1 First array
     * @param array $array2 Second array
     * @return array Intersection
     */
    public static function intersectByKey(array $array1, array $array2): array
    {
        return array_intersect_key($array1, $array2);
    }

    /**
     * Get array difference by key
     * 
     * @param array $array1 First array
     * @param array $array2 Second array
     * @return array Difference
     */
    public static function diffByKey(array $array1, array $array2): array
    {
        return array_diff_key($array1, $array2);
    }

    /**
     * Rename array key
     * 
     * @param array $array Array to modify
     * @param string $oldKey Old key name
     * @param string $newKey New key name
     * @return array Modified array
     */
    public static function renameKey(array $array, string $oldKey, string $newKey): array
    {
        if (!array_key_exists($oldKey, $array)) {
            return $array;
        }

        $result = [];
        
        foreach ($array as $key => $value) {
            $resultKey = ($key === $oldKey) ? $newKey : $key;
            $result[$resultKey] = $value;
        }

        return $result;
    }

    /**
     * Wrap single value in array if not already array
     * 
     * @param mixed $value Value to wrap
     * @return array Array containing the value
     */
    public static function wrap($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        return $value === null ? [] : [$value];
    }

    /**
     * Get first value from array
     * 
     * @param array $array Array to get from
     * @param mixed $default Default value if empty
     * @return mixed First value or default
     */
    public static function first(array $array, $default = null)
    {
        return empty($array) ? $default : reset($array);
    }

    /**
     * Get last value from array
     * 
     * @param array $array Array to get from
     * @param mixed $default Default value if empty
     * @return mixed Last value or default
     */
    public static function last(array $array, $default = null)
    {
        return empty($array) ? $default : end($array);
    }

    /**
     * Get random value from array
     * 
     * @param array $array Array to get from
     * @param mixed $default Default value if empty
     * @return mixed Random value or default
     */
    public static function random(array $array, $default = null)
    {
        if (empty($array)) {
            return $default;
        }

        $key = array_rand($array);
        return $array[$key];
    }

    /**
     * Check if array is associative
     * 
     * @param array $array Array to check
     * @return bool True if associative
     */
    public static function isAssociative(array $array): bool
    {
        return array_keys($array) !== range(0, count($array) - 1);
    }

    /**
     * Convert array to query string
     * 
     * @param array $array Array to convert
     * @param string $prefix Prefix for nested arrays
     * @return string Query string
     */
    public static function toQueryString(array $array, string $prefix = ''): string
    {
        return http_build_query($array, $prefix, '&', PHP_QUERY_RFC3986);
    }
}