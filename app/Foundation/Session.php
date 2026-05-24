<?php
namespace App\Foundation;
class Session
{
public function __construct()
{
  if(session_status() === PHP_SESSION_NONE){
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $host = explode(':', $host)[0];
    $parts = explode('.', $host);
    
    // If we have at least 2 parts (e.g., blogify.dev), set cookie domain to .blogify.dev
    // Do NOT do this for production domains, to prevent public suffix list blocking
    if (count($parts) >= 2 && !filter_var($host, FILTER_VALIDATE_IP) && $host !== 'localhost' && !is_production()) {
        $domain = '.' . implode('.', array_slice($parts, -2));
        $isHttps = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || 
                   (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
        
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => $domain,
            'secure' => $isHttps,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    }
    session_start();
  }
}
public function set(string $key,$value):void{
  $_SESSION[$key] = $value;
}
public function get(string $key){
  return $_SESSION[$key] ?? null;
}
public function remove(string $key):void{
  unset($_SESSION[$key]);
}
public function setFlash(string $type, string $message): void {
    $_SESSION['flash'][$type] = $message;
}

public function getFlash(string $type) {
    $message = $_SESSION['flash'][$type] ?? null;
    unset($_SESSION['flash'][$type]);
    return $message;
}

public function hasFlash(string $type): bool {
    return isset($_SESSION['flash'][$type]);
}

public function destroy():void{
  session_destroy();
}
}
