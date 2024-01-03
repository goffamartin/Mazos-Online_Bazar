<?php

/**
 * Retrieves a value from a specified array after sanitizing it.
 * If the key is not set in the array, an empty string is returned.
 *
 * @param string $key The key to look for in the specified array.
 * @param array $source The array to search for the specified key in.
 * @return string The sanitized value from the array or an empty string if the key is not set.
 */
function getFormData($key, $source)
{
    if(isset($source[$key]))
        return htmlspecialchars($source[$key]);
    return "";
}