<?php

/**
 * Returns the URL for a Vite asset.
 *
 * @param string $path
 * @return string
 */
function vite_asset(string $path)
{
    $manifestPath = BASE_PATH . '/public/build/.vite/manifest.json';
    
    if (file_exists($manifestPath)) {
        $manifest = json_decode(file_get_contents($manifestPath), true);
        if (isset($manifest[$path])) {
            return '/build/' . $manifest[$path]['file'];
        }
        if ($path === '@vite/client') return '';
    }

    $baseUrl = getenv('VITE_URL') ?: ($_ENV['VITE_URL'] ?? 'http://localhost:5173');
    return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
}

function vite_css(string $path)
{
    $manifestPath = BASE_PATH . '/public/build/.vite/manifest.json';
    
    if (file_exists($manifestPath)) {
        $manifest = json_decode(file_get_contents($manifestPath), true);
        if (isset($manifest[$path]['css'])) {
            return '/build/' . $manifest[$path]['css'][0];
        }
    }
    return '';
}

function post_url(string $username, string $slug)
{
    $host = $_SERVER['HTTP_HOST'] ?? '';
    // If we are on Render (or an IP/localhost without subdomains), use the path-based profile route
    if (str_ends_with($host, '.onrender.com') || filter_var(explode(':', $host)[0], FILTER_VALIDATE_IP) || str_starts_with($host, 'localhost')) {
        // We will just return the slug, because PostController@show fetches by slug
        return "/{$slug}";
    }
    return "http://{$username}.blogify.dev/{$slug}";
}

function user_profile_url(string $username)
{
    $host = $_SERVER['HTTP_HOST'] ?? '';
    if (str_ends_with($host, '.onrender.com') || filter_var(explode(':', $host)[0], FILTER_VALIDATE_IP) || str_starts_with($host, 'localhost')) {
        return "/{$username}";
    }
    return "http://{$username}.blogify.dev/";
}

function home_url()
{
    $host = $_SERVER['HTTP_HOST'] ?? '';
    if (str_ends_with($host, '.onrender.com')) {
        return "/";
    }
    return "http://blogify.dev/";
}
