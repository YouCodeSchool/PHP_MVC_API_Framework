# PMA : PHP MVC API Framework
Simple PHP framework that manages either the API, WEB or both sides of an application with an MVC architecture and structure.

The API is handled through a Middleware and partially with MVC (only Controllers and Models) with a JWT Token Authentication.

The WEB portion is handled fully with MVC with simple flexible routing and a customized Views system (CVS : Component View System).

# Documentation Table of Contents
<!--ts-->
* [Installation](#installation)
* [Usage](#usage)
* [Environment Variables](#environment-variables)
* [Constants](#constants)
* [Routing](#routing)
  * [Web](#web)
  * [Api](#api)
* [Notes](#notes)
<!--te-->

# Installation

[Composer](https://getcomposer.org/) is required.

Install dependencies and other packages
```bash
composer install
```

[Back to top](#documentation-table-of-contents)

# Usage

Run PHP's Local Development Server with this custom command:
```bash
composer serve
```
or:
```bash
php -S localhost:8000 -t public
```
or navigate to public folder then run the server:
```bash
cd public
php -S localhost:8080
```

[Back to top](#documentation-table-of-contents)


# Environment variables

Rename or copy .env.example file into .env and customize or add your own Database name, Table and Columns names, API Secret Key: 
```
# Database Credentials: *fill with your proper database information
HOST=
DB_NAME=
USERNAME=
PASSWORD=

# Database Account Table:
ACCOUNTS_TABLE=

# Database Account Columns:
ACCOUNTS_ID=
ACCOUNTS_NAME=
ACCOUNTS_PASSWORD=

# Database Audience Table: *The recipient table for account for 'aud' in JWT token payload
AUDIENCE_TABLE=
# Database Other Tables: *Other tables you may need, as argument for your CRUD operations in controller methods
EXAMPLE_TABLE=

# Data Source Name: *do not modify! $_ENV['DSN'] is used as first argument for your PDO connection
DSN=mysql:host=${HOST};dbname=${DB_NAME}

# API Token Authentication Secret Keys: *fill with your own key or add new keys as needed
SECRET_KEY=
```

[Back to top](#documentation-table-of-contents)

# Constants

Open `app/config/defconst.php` to find multiple defined constants for error handling etc... you can add your own with their respective code if needed

```php
<?php

/* Defined Constants for various purposes */
// * You can add your own error and success messages with their codes here for use

// Data Types
define('BOOLEAN', '1');
define('INTEGER', '2');
define('STRING', '3');

/* Error Codes */
// Requests
define('REQUEST_METHOD_NOT_VALID', 100);
define('REQUEST_CONTENT_TYPE_NOT_VALID', 101);
define('REQUEST_NOT_VALID', 102);
// Methods
define('METHOD_NAME_REQUIRED', 103);
define('METHOD_PARAMS_REQUIRED', 104);
define('METHOD_DOES_NOT_EXIST', 105);
// Parameters
define('VALIDATE_PARAMETER_REQUIRED', 106);
define('VALIDATE_PARAMETER_DATATYPE', 107);
// Authentication
define('INVALID_ACCOUNT', 108);
define('ACCOUNT_NOT_ACTIVE', 109);
define('ACCOUNT_NOT_FOUND', 110);

// Response Codes 
define('RESPONSE_MESSAGE', 200);

// JWT Token Authentication Errors
define('JWT_PROCESSING_ERROR', 300);
define('AUTHORIZATION_HEADER_NOT_FOUND', 301);
define('ACCESS_TOKEN_ERRORS',	302);

?>
```

[Back to top](#documentation-table-of-contents)


# Routing

Open `public/index.php`

```php
<?php

use App\Controller\WebController;
use App\Controller\ExampleApiController;
use App\Controller\UsersApiController;
use App\Application;

require_once __DIR__ . '../../composer_vendor/autoload.php';

$app = new Application(dirname(__DIR__));

// * WebController manages the Web side of the framework

// Web routes for views : GET
$app->router->get('/', [WebController::class, 'home']);
$app->router->get('/home', [WebController::class, 'home']);
$app->router->get('/login', [WebController::class, 'getLogin']);
// Web routes for views : POST
$app->router->post('/login', [WebController::class, 'postLogin']);

// * Any ExampleApiController manages the API side of the framework with the Middleware

// API routes for endpoints : GET
$app->router->get('/endpoint', [ExampleApiController::class, 'processExampleMethods']);

// API routes for endpoints : POST
$app->router->post('/endpoint', [ExampleApiController::class, 'processExampleMethods']);

$app->run();
?>
```

You can add a new route based on the examples by specifiying the request method (GET or POST), the url, the Controller and it's method

## Web

Some method examples that calls a view renderer to render the pages for GET and a POST example to show data sent from a form.

`app/controllers/WebController.php`

```php
<?php
// Route methods for Web Views in index.php
namespace App\Controller;

use App\Request;

class WebController extends ViewsController {
    // ? Below are just examples for testing purposes
    // Passing params to view renderer if needed : GET
    public static function home() {
        $params = [
            'name' => "User"
        ];
        return ViewsController::render('home', $params);
    }
    // Shows a login form : GET
    public static function getLogin() {
        return ViewsController::render('login');
    }
    /**  Handles the login form : POST
    * @param string App\Request $request
    */
    public static function postLogin(Request $request) {
        $body = $request->getBody();
        echo '<pre>';
        print_r($body);
        echo '</pre>';
    }
}
?>
```

### CVS: Component View System *Work in progress

The view are handled with a system that lays a main layout with some placeholders (components) that gets filled with appropriate content.

Navigating to http://localhost:8080/ or http://localhost:8080/home will display a "Hello World!" and a welcome message.

Arguments passed into the render method will take the `app/views/layout/main.php` as layout and replace the components inside with their respective content:

```php
<!DOCTYPE html>
<html lang="en">

<head>
    {{head}}
    <meta name="description" content="Index Page Description">
</head>

<body>
    {{content}}
</body>

</html>
```

In this example, the {{head}} component is a static component since it contains content always needed in all pages `app/views/components/static/head.php`:

```php
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>App Title</title>
<link rel="icon" href="">
<link rel="stylesheet" href="">
<script src="" defer></script>
```

The {{content}} component in the body tag is a dynamic component that depends on the argument in the render method which is also the page requested in the url with the help of the routing system.

`app/views/components/dynamic/home.php`

```php
<h1>Hello World!</h1>
<h3>Welcome <?php echo $name ?></h3>
```

Navigating to http://localhost:8080/login will display a form:

`app/views/components/dynamic/login.php`

```php
<h1>Login</h1>
<form action="" method="POST">
<input type="text" name="username">
<input type="text" name="password">
<button type="submit">Submit</button>
</form>
```

Navigating to a page that doesn't exists will render an error 404 message:

`app/views/components/dynamic/_404.php`

```php
<h1>Error 404 - The page you are requesting doesn't exist.</h1>
```

- The purpose of this system is to avoid repepetive sections of code for elements and treat them as reused components. The idea is based on multiple frontend frameworks.
- For styling components either link to a css file (global or specific to the component) or open style tags inside the component file.

[Back to top](#documentation-table-of-contents)

## Api 

For the API, the routes specified in `public/index.html` are the endpoints. You can decide to have one endpoint (means one API controller holding all the methods) or multiple depending on each API Controller. The method specified in `public/index.html` is a special method inherited from the Middleware that processes a method sent with the data to the API to invoke it on the Controller object if it exists.

Example of data sent:
````json
{
     "method": "generateToken",
     "params": {
         "account_name": "admin",
         "account_password": "admin123**"
     }
}
````
In this example, the Middleware will order the Controller to execute the "generateToken" method declared in said Controller.

### Api Controller

`app/controllers/ExampleApiController.php`

```php
<?php
// Create an API Controller based on this template (example: UsersApiController)
namespace App\Controller;

use App\Config\Middleware;
// Replace with the model needed
// use App\Model\ExampleModel;
use PDO;
use Exception;

require_once __DIR__ . '../../../composer_vendor/autoload.php';

use \Firebase\JWT\JWT;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable('../');
$dotenv->load();

// You list all your methods here that get sent as part of a data object to your endpoint.

class ExampleApiController extends Middleware
{
    // Array to hold any data you need about account
    private $account_info = [];

    public function __construct()
    {
        parent::__construct();
    }

    // SQL SELECT statement for verifying if account credentials are correct to sign in and allow token generation
    public function sqlVerifyAccount($account_name, $account_name_col, $account_password, $account_password_col, $table)
    {
        $sql = "SELECT * FROM $table WHERE $account_name_col = :account_name AND $account_password_col = :account_password";
        $stmt = $this->db_conn->prepare($sql);
        $stmt->bindParam(':account_name', $account_name);
        $stmt->bindParam(':account_password', $account_password);
        $stmt->execute();

        $account = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!is_array($account)) {
            $this->returnResponse(INVALID_ACCOUNT, 'Account Name or Password is incorrect.');
        }
        $this->account_info = $account;
    }

    // Generating token for login
    public function generateToken()
    {
        $account_name = $this->validateParams('account_name', $this->param['account_name'], STRING);
        $account_password = $this->validateParams('account_password', $this->param['account_password'], STRING);
        try {

            $this->sqlVerifyAccount($account_name, $_ENV['ACCOUNTS_NAME'], $account_password, $_ENV['ACCOUNTS_PASSWORD'], $_ENV['ACCOUNTS_TABLE']);

            $iss = $_ENV['HOST'];
            $iat = time();
            // Token expiration time in seconds: 60 * 30 = 30min; 60 * 60 = 1hour...
            $exp = $iat + (60 * 30);
            $aud = $_ENV['AUDIENCE_TABLE'];
            // Array containing any account data needed from token you can add an entry based on needs
            $account_data = array(
                $_ENV['ACCOUNTS_ID'] => $this->account_info[$_ENV['ACCOUNTS_ID']],
                $_ENV['ACCOUNTS_NAME'] => $this->account_info[$_ENV['ACCOUNTS_NAME']]
            );

            $payload = array(
                "iss" => $iss,
                "iat" => $iat,
                "exp" => $exp,
                "aud" => $aud,
                "data" => $account_data
            );

            $jwt = JWT::encode($payload, $_ENV['SECRET_KEY']);
            $data = ['token' => $jwt];
            echo $data['token'];
            
            // Checking if the token has been generated successfully
            // $this->returnResponse(RESPONSE_MESSAGE, $data);

        } catch (Exception $e) {
            $this->throwError(JWT_PROCESSING_ERROR, $e->getMessage());
        }
    }

    // List your methods below that will be called in your fetch along with or without parameters.
    // * Add : '$this->validateToken();' as your first line in your method if it requires a token
?>
```

### Middleware 

The Middleware handles everything from requests, content-type, authorization header and token. Validates data, methods and parameters.

Below the method that processes the Controller methods sent part of the data:

`app/config/Middleware.php`

```php
<?php
    public function processExampleMethods()
    {
        $exampleApiObj = new \App\Controller\ExampleApiController();
        $exampleApiMethod = new ReflectionMethod('App\Controller\ExampleApiController', $this->method);
        if (!method_exists($exampleApiObj, $this->method)) {
            $this->throwError(METHOD_DOES_NOT_EXIST, "Method does not exist.");
        }
        $exampleApiMethod->invoke($exampleApiObj);
    }
?>
````

[Back to top](#documentation-table-of-contents)

# Notes
- This is mainly a personal project made for the purpose of learning and setting up a base template for future projects and also a plarform for future updates and added features.
- Anyone is free to use it to learn or for projects, feedback is welcome and if you find any problem you can open an issue to discuss and fix it.
- The views system is a work in progress and experimental.
