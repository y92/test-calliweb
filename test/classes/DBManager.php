<?php

class DBManager
{
    public const QUERY_SUCCESS = 0;

    public const DB_ERROR = -1;

    public const INVALID_ARGUMENT = -2;

    public const ALREADY_EXISTS = -3;

    public const NOT_EXISTS = -4;

    public const FK_NOT_EXISTS = -5;

    private const USERS_TABLE = "Users";

    private const ADDRESSES_TABLE = "Addresses";
    
    private const TOKENS_TABLE = "Tokens";

    private static $instance;

    private $pdo;

    private function __construct()
    {
        $this->pdo = $this->connectToDb();
    }

    public static function getInstance() : DBManager
    {
        return self::$instance = self::$instance ?? new DBManager();
    }

    private function connectToDb() : PDO
    {
        $data = json_decode(file_get_contents(__DIR__.'/../../mysql.json'), true);
        $user = $data['user'];
        $db = $data['database'];
        $pwd = $data['password'];
        return new PDO("mysql:host=localhost;dbname=$db;charset=utf8", $user, $pwd, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    }

    public function existsMember(string $email) : ?bool
    {
        try {
            $query = $this->pdo->prepare("SELECT * FROM ".self::USERS_TABLE." WHERE trim(lower(email))=trim(lower(?))");
            $query->execute(array($email));
    
            if ($line = $query->fetch(PDO::FETCH_ASSOC)) {
                return true;
            }
    
            return false;
        }
        catch(PDOException $e) {
            return null;
        }

    }

    public function existsMemberId(int $id) : ?bool
    {
        try {
            $query = $this->pdo->prepare("SELECT * FROM ".self::USERS_TABLE." WHERE id=?");
            $query->execute(array($id));
    
            if ($line = $query->fetch(PDO::FETCH_ASSOC)) {
                return true;
            }
    
            return false;
        }
        catch(PDOException $e) {
            return null;
        }        
    }

    public function findMemberForLogin(string $email, string $pwd) : array
    {
        $res = [];

        try {
            $query = $this->pdo->prepare("SELECT * FROM ".self::USERS_TABLE." WHERE trim(lower(email))=trim(lower(?)) AND password=PASSWORD(?)");
            $query->execute(array($email, $pwd));

            if ($line = $query->fetch(PDO::FETCH_ASSOC)) {
                $res['id'] = $line['id'];
                $res['code'] = self::QUERY_SUCCESS;
            }
            else {
                $res['code'] = self::NOT_EXISTS;
            }

            return $res;
        }
        catch(PDOException $e) {
            $res['code'] = self::DB_ERROR;
            return $res;
        }


    }

    public function insertMember(string $email, string $pwd) : int
    {
        $emailT = trim($email);

        if (strlen($emailT) < 1 || !filter_var($emailT, FILTER_VALIDATE_EMAIL) || strlen($pwd) < 1) {
            return self::INVALID_ARGUMENT;
        }

        if ($this->existsMember($emailT)) {
            return self::ALREADY_EXISTS;
        }

        try {
            $query = $this->pdo->prepare("INSERT INTO ".self::USERS_TABLE."(email, password) VALUES(trim(lower(?)), PASSWORD(?))");
            $query->execute(array($emailT, $pwd));

            return self::QUERY_SUCCESS;
        }
        catch (PDOException $e) {
            return self::DB_ERROR;
        }
    }

    public function insertAddress(int $userId, ?string $prefix, ?string $lastName, ?string $firstName, ?string $email, ?string $phoneNumber, ?string $addrL1, ?string $addrL2, ?string $postalCode, ?string $city, ?string $country) : int
    {
        $emailT = trim($email);

        if ($this->existsMemberId($userId) === false) {
            return self::FK_NOT_EXISTS;
        }

        if ($email != null && !filter_var($emailT, FILTER_VALIDATE_EMAIL)) {
            return self::INVALID_ARGUMENT;
        }

        try {
            $query = $this->pdo->prepare("INSERT INTO ".self::ADDRESSES_TABLE."(owner, prefix, lastName, firstName, email, phoneNumber, addrL1, addrL2, postalCode, city, country) VALUES(?, trim(?), trim(?), trim(?), trim(?), trim(?), trim(?), trim(?), trim(?), trim(?), trim(?))");
            $query->execute(array($userId, $prefix, $lastName, $firstName, $email, $phoneNumber, $addrL1, $addrL2, $postalCode, $city, $country));

            return self::QUERY_SUCCESS;
        }
        catch(PDOException $e) {
            return self::DB_ERROR;
        }
    }

    public function updateAddress(int $id, ?string $prefix, ?string $lastName, ?string $firstName, ?string $email, ?string $phoneNumber, ?string $addrL1, ?string $addrL2, ?string $postalCode, ?string $city, ?string $country) : int
    {
        $emailT = trim($email);

        $addr = $this->getAddress($id);

        if ($addr['code'] == self::NOT_EXISTS) {
            return self::FK_NOT_EXISTS;
        }

        if ($email != null && !filter_var($emailT, FILTER_VALIDATE_EMAIL)) {
            return self::INVALID_ARGUMENT;
        }

        try {
            $query = $this->pdo->prepare("UPDATE ".self::ADDRESSES_TABLE." SET prefix=trim(?), lastName=trim(?), firstName=trim(?), email=trim(?), phoneNumber=trim(?), addrL1=trim(?), addrL2=trim(?), postalCode=trim(?), city=trim(?), country=trim(?) WHERE id=?");
            $query->execute(array($prefix, $lastName, $firstName, $email, $phoneNumber, $addrL1, $addrL2, $postalCode, $city, $country, $id));

            return self::QUERY_SUCCESS;
        }
        catch(PDOException $e) {
            return self::DB_ERROR;
        }
    }

    public function deleteAddress(int $id) : int
    {
        try {
            $query = $this->pdo->prepare("DELETE FROM ".self::ADDRESSES_TABLE." WHERE id=?");
            $query->execute(array($id));

            return self::QUERY_SUCCESS;
        }
        catch(PDOException $e) {
            return self::DB_ERROR;
        }
    }

    public function getAddress(int $id) : array
    {
        $res = [];

        try {
            $query = $this->pdo->prepare("SELECT * FROM ".self::ADDRESSES_TABLE." WHERE id=?");
            $query->execute(array($id));

            if ($line = $query->fetch(PDO::FETCH_ASSOC)) {
                $res['code'] = self::QUERY_SUCCESS;
                $res['address'] = $line;
            }
            else {
                $res['code'] = self::NOT_EXISTS;
            }
            return $res;
        }
        catch(PDOException $e) {
            $res['code'] = self::DB_ERROR;
            return $res;
        }
    }

    public function getAddressesFromUser(int $user) : array
    {
        $res = [];

        if ($this->existsMemberId($user) === false) {
            $res['code'] = self::FK_NOT_EXISTS;
            return $res;
        }
        else {
            try {
                $query = $this->pdo->prepare("SELECT * FROM ".self::ADDRESSES_TABLE." WHERE owner=? ORDER BY id DESC");
                $query->execute(array($user));

                $addresses = [];

                while ($line = $query->fetch(PDO::FETCH_ASSOC)) {
                    $addresses[] = $line;
                }

                $res['code'] = self::QUERY_SUCCESS;
                $res['addresses'] = $addresses;

                return $res;
            }
            catch(PDOException $e) {
                $res['code'] = self::DB_ERROR;
                return $res;
            }
        }
    }

    private function existsToken(string $token) : bool
    {
        $query = $this->pdo->prepare("SELECT * FROM ".self::TOKENS_TABLE." WHERE trim(lower(token))=trim(lower(?))");
        $query->execute(array($token));

        if ($line = $query->fetch(PDO::FETCH_ASSOC)) {
            return true;
        }

        return false;
    }

    public function isValidToken(string $token) : ?bool
    {
        try{
            $query = $this->pdo->prepare("SELECT * FROM ".self::TOKENS_TABLE." WHERE trim(lower(token))=trim(lower(?)) AND validUntil >= NOW()");
            $query->execute(array($token));

            if ($line = $query->fetch(PDO::FETCH_ASSOC)) {
                return true;
            }

            return false;
        }
        catch(PDOException $e) {
            return null;
        }
    }

    public function insertToken(string $token, int $duration=300) : int
    {
        if (strlen(trim($token)) < 1 || $duration <= 0) {
            return self::INVALID_ARGUMENT;
        }
        if ($this->existsToken($token)) {
            return self::ALREADY_EXISTS;
        }
        try {
            $query = $this->pdo->prepare("INSERT INTO ".self::TOKENS_TABLE."(token, createdOn, validUntil) VALUES(trim(lower(?)), NOW(), DATE_ADD(NOW(), INTERVAL ? SECOND))");
            $query->execute(array($token, $duration));

            return self::QUERY_SUCCESS;
        }
        catch(PDOException $e) {
            return self::DB_ERROR;
        }
    }

    public function deleteToken(string $token) : int
    {
        try {
            $query = $this->pdo->prepare("DELETE FROM ".self::TOKENS_TABLE." WHERE trim(lower(token))=trim(lower(?)");
            $query->execute(array($token));

            return self::QUERY_SUCCESS;
        }
        catch(PDOException $e) {
            return self::DB_ERROR;
        }
    }
}