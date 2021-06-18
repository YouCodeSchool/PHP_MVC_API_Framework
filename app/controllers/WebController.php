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