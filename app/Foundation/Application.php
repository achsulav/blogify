<?php
namespace App\Foundation;
use App\Foundation\Database;
use App\Foundation\Router;
use App\Foundation\Session;
class Application
{
    public static Application $app;
    protected Router $router;
    public Session $session;
    public Database $db;

    public function __construct()
    {
      if (file_exists(__DIR__ . '/../../.env')) {
          $lines = file(__DIR__ . '/../../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
          foreach ($lines as $line) {
              if (strpos(trim($line), '#') === 0) continue;
              list($name, $value) = explode('=', $line, 2) + [null, null];
              if ($name !== null && $value !== null) {
                  $name = trim($name);
                  $value = trim($value, " \t\n\r\0\x0B\"'");
                  if (!array_key_exists($name, $_ENV) && !array_key_exists($name, $_SERVER) && getenv($name) === false) {
                      putenv(sprintf('%s=%s', $name, $value));
                      $_ENV[$name] = $value;
                      $_SERVER[$name] = $value;
                  }
              }
          }
      }

      self::$app = $this;
      $this->session = new Session();
      $this->router = new Router();
      $config = require __DIR__ . '/../Config/database.php';
      $this->db = new Database($config);
      
      $this->registerRoutes();
    }
    protected  function registerRoutes():void
    {

        // Register routes here
        // ShowLogin => To render the Page
        // Login=> To perform login action
        

        $this->router->get('/login','AuthController@showLogin',['guest']);
        $this->router->post('/login','AuthController@Login',['guest']);

        $this->router->get('/register','AuthController@showRegister');
        $this->router->post('/register','AuthController@Register');

        $this->router->get('/verify-otp', 'VerificationController@showVerifyForm');
        $this->router->post('/verify-otp', 'VerificationController@verify');

        $this->router->get('/logout','AuthController@Logout',['auth']);
        $this->router->get('/', 'PageController@home');
        $this->router->get('/test','PageController@testRedirect');
        $this->router->get('/create-user','PageController@createUser',['auth']);

        $this->router->get('/dashboard','DashboardController@index',['auth']);

        $this->router->get('/create-post','PostController@create',['auth']);
        $this->router->post('/store-post','PostController@store',['auth']);

        $this->router->get('/blog','PostController@index');
        $this->router->get('/{slug}','PostController@show');
        $this->router->post('/upload-image','PostController@uploadImage',['auth']);

        $this->router->get('/post/edit/{id}', 'PostController@edit',['auth']);
        $this->router->post('/post/update/{id}', 'PostController@update',['auth']);

        $this->router->post('/post/delete','PostController@delete',['auth']);


        // Note: /{slug} is already registered above, which will conflict with /{username}.
        // We will handle this in PostController@show by checking for users if a post is not found.
        // $this->router->get('/{username}','UserController@profile');

        $this->router->post('/comment/store','CommentController@store',['auth']);
        $this->router->post('/comment/update','CommentController@update',['auth']);
        $this->router->post('/comment/delete','CommentController@delete',['auth']);

    }

    public function getSubdomain(): ?string
    {
        $host = $_SERVER['HTTP_HOST'] ?? '';
        $host = explode(':', $host)[0];

        // Ignore IP addresses and localhost
        if (filter_var($host, FILTER_VALIDATE_IP) || $host === 'localhost') {
            return null;
        }

        $parts = explode('.', $host);
        
        // If host is user.blogify.dev -> parts is ['user', 'blogify', 'dev']
        // We only return a subdomain if there are at least 3 parts.
        if (count($parts) >= 3) {
            return $parts[0];
        }

        return null;
    }


    public function run():void
    {
        $this->router->resolve();
    }
}
