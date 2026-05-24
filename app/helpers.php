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

function is_production(): bool
{
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $hostWithoutPort = explode(':', $host)[0];
    
    if ($hostWithoutPort === 'localhost' || filter_var($hostWithoutPort, FILTER_VALIDATE_IP) || str_ends_with($hostWithoutPort, 'blogify.dev')) {
        return false;
    }
    return true;
}

function post_url(string $username, string $slug)
{
    // If we are in production (Render or custom domain), use the path-based profile route
    if (is_production()) {
        // We will just return the slug, because PostController@show fetches by slug
        return "/{$slug}";
    }
    return "http://{$username}.blogify.dev/{$slug}";
}

function user_profile_url(string $username)
{
    if (is_production()) {
        return "/{$username}";
    }
    return "http://{$username}.blogify.dev/";
}

function home_url()
{
    if (is_production()) {
        return "/";
    }
    return "http://blogify.dev/";
}
