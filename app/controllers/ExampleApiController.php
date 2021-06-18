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

// * example of data object for GET:
// {
//     "method": "getUsers",
// }

// * example of data object for POST:
// {
//     "method": "generateToken",
//     "params": {
//         "account_name": "admin",
//         "account_password": "admin123**"
//     }
// }

class ExampleApiController extends Middleware
{
    // Array to hold any data you need about account
    private $account_info = [];

    public function __construct()
    {
        parent::__construct();
    }

    /** SQL SELECT statement for verifying if account credentials are correct to sign in and allow token generation
    * @param string $account_name account name
    * @param string $account_name_col ENV variable of account name column in database
    * @param string $account_password account password
    * @param string $account_password_col ENV variable of account passwod column in database
    * @param string $table ENV variable of table name of accounts in database
    */
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

    // public function createData() {
    //     $param = $this->validateParams('param', $this->param['param'], STRING or INTEGER or BOOLEAN);
    // * if you are hashing a password for account creation
    //     $password = $this->validateParams('password', $this->param['password'], STRING);
    //     $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    //     $data = new ExampleModel;
    //     $data->setParam($param);
    // * for your hashed password
    //     $data->setPassword($hashed_password);
    //     if($data->create($_ENV['EXAMPLE_TABLE']) != true) {
    //         $message = 'Failed to create data.';
    //     } else {
    //         $message = "Data created successfully.";
    //     }
    //     $this->returnResponse(RESPONSE_MESSAGE, $message);
    // }

    // public function readData() {
    //     $data = new ExampleModel;
    //     if($data->read($_ENV['EXAMPLE_TABLE']) != true) {
    //         $message = 'Failed to fetch data.';
    //     } else {
    //         $message = "Data fetched successfully.";
    //     }
    //     $this->returnResponse(RESPONSE_MESSAGE, $message);
    // }

    // public function updateData() {
    //     $this->validateToken();
    //     $id = $this->validateParams('id', $this->param['id'], INTEGER);
    //     $param = $this->validateParams('param', $this->param['param'], STRING or INTEGER or BOOLEAN);
    //     $data = new ExampleModel;
    //     $data->setId($id);
    //     $data->setParam($param);
    //     if($data->update($_ENV['EXAMPLE_TABLE']) != true {
    //         $message = 'Failed to update data.';
    //     } else {
    //         $message = "Data updated successfully.";
    //     }
    //     $this->returnResponse(RESPONSE_MESSAGE, $message);
    // }

    // public function deleteData() {
    //     $this->validateToken();
    //     $id = $this->validateParams('id', $this->param['id'], INTEGER);
    //     $data = new ExampleModel;
    //     $data->setId($id);
    //     if($data->delete($_ENV['EXAMPLE_TABLE']) != true) {
    //         $message = 'Failed to delete data.';
    //     } else {
    //         $message = "Data deleted successfully.";
    //     }
    //     $this->returnResponse(RESPONSE_MESSAGE, $message);
    // }
}
