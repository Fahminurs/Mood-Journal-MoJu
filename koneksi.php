<?php  

class Database {  
    private $host = "localhost";  
    private $username = "root";  
    private $password = "";  
    private $database = "moju";  
    private $conn;  
    private $logger;  

    public function __construct() {  
        $this->logger = Logger::getInstance();  
    }  

    public function connect() {  
        try {  
            $this->conn = new PDO(  
                "mysql:host=" . $this->host . ";dbname=" . $this->database,  
                $this->username,  
                $this->password,  
                array(  
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,  
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,  
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"  
                )  
            );  

            $this->logger->logActivity(  
                'DATABASE',  
                'Database connection established successfully'  
            );  

            return $this->conn;  

        } catch(PDOException $e) {  
            $this->logger->logError(  
                'DATABASE_ERROR',  
                'Connection failed',  
                $e  
            );  
            
            $this->handleError($e);  
            return false;  
        }  
    }  

    private function handleError($e) {  
        if (getenv('ENVIRONMENT') === 'development') {  
            die("Connection failed: " . $e->getMessage());  
        } else {  
            die("Maaf, terjadi kesalahan pada sistem. Tim kami sedang menanganinya.");  
        }  
    }  

    public function close() {  
        $this->logger->logActivity(  
            'DATABASE',  
            'Database connection closed'  
        );  
        $this->conn = null;  
    }  
}  


class Logger {  
    private $logPath;  
    private static $instance = null;  

    private function __construct() {  
        // Buat direktori logs jika belum ada  
        $this->logPath = dirname(__FILE__) . '/logs';  
        if (!file_exists($this->logPath)) {  
            mkdir($this->logPath, 0777, true);  
        }  
    }  

    public static function getInstance() {  
        if (self::$instance === null) {  
            self::$instance = new Logger();  
        }  
        return self::$instance;  
    }  

    public function logActivity($type, $message, $data = null) {  
        $timestamp = date('Y-m-d H:i:s');  
        $logFile = $this->logPath . '/activity_' . date('Y-m-d') . '.log';  
        
        $logEntry = "[{$timestamp}] [{$type}] {$message}";  
        if ($data) {  
            $logEntry .= "\nData: " . json_encode($data, JSON_PRETTY_PRINT);  
        }  
        $logEntry .= "\n--------------------\n";  

        file_put_contents($logFile, $logEntry, FILE_APPEND);  
    }  

    public function logError($type, $message, $exception = null) {  
        $timestamp = date('Y-m-d H:i:s');  
        $logFile = $this->logPath . '/error_' . date('Y-m-d') . '.log';  
        
        $logEntry = "[{$timestamp}] [{$type}] {$message}";  
        if ($exception) {  
            $logEntry .= "\nException: " . $exception->getMessage();  
            $logEntry .= "\nFile: " . $exception->getFile();  
            $logEntry .= "\nLine: " . $exception->getLine();  
            $logEntry .= "\nTrace: " . $exception->getTraceAsString();  
        }  
        $logEntry .= "\n--------------------\n";  

        file_put_contents($logFile, $logEntry, FILE_APPEND);  
    }  
}  



// Helper functions  

function getConnection() {  
    static $db = null;  
    if ($db === null) {  
        $db = new Database();  
    }  
    return $db->connect();  
}  

function executeQuery($sql, $params = [], $description = '') {  
    $logger = Logger::getInstance();  
    try {  
        $start = microtime(true);  
        
        $db = getConnection();  
        
        // Persiapkan statement dengan menggunakan prepared statement  
        $stmt = $db->prepare($sql);  
        
        // Binding parameter dengan tipe yang tepat  
        foreach ($params as $index => $param) {  
            // Tentukan tipe parameter  
            $paramType = PDO::PARAM_STR; // Default ke string  
            
            if (is_int($param)) {  
                $paramType = PDO::PARAM_INT;  
            } elseif (is_bool($param)) {  
                $paramType = PDO::PARAM_BOOL;  
            } elseif (is_null($param)) {  
                $paramType = PDO::PARAM_NULL;  
            }  
            
            // Binding parameter dengan indeks berbasis 1  
            $stmt->bindValue($index + 1, $param, $paramType);  
        }  
        
        // Eksekusi statement  
        $executeResult = $stmt->execute();  
        
        $duration = round((microtime(true) - $start) * 1000, 2); // dalam milliseconds  
        
        // Log query execution  
        $logger->logActivity('QUERY', $description ?: 'Query executed', [  
            'sql' => $sql,  
            'params' => $params,  
            'duration' => $duration . 'ms',  
            'affected_rows' => $stmt->rowCount()  
        ]);  
        
        return $stmt;  
        
    } catch (PDOException $e) {  
        $logger->logError('QUERY_ERROR', 'Query execution failed', $e);  
        
        // Log detail query untuk debugging  
        $logger->logActivity('QUERY_DEBUG', 'Failed Query Details', [  
            'sql' => $sql,  
            'params' => $params,  
            'error' => $e->getMessage()  
        ]);  
        
        throw $e;  
    }  
}  

function selectQuery($sql, $params = [], $description = '') {  
    $logger = Logger::getInstance();  
    try {  
        // Eksekusi query dengan parameter terikat  
        $stmt = executeQuery($sql, $params, $description);  
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);  
        
        // Log informasi query  
        $logger->logActivity('SELECT', $description ?: 'Select query executed', [  
            'sql' => $sql,  
            'params' => $params,  
            'rows_returned' => count($result)  
        ]);  
        
        return $result;  
        
    } catch (PDOException $e) {  
        $logger->logError('SELECT_ERROR', 'Select query failed', $e);  
        throw $e;  
    }  
} 


// Set timezone  
date_default_timezone_set('Asia/Jakarta');  

// Contoh penggunaan:  
/*  
try {  
    // Select dengan logging  
    $users = selectQuery(  
        "SELECT * FROM user WHERE role = ?",  
        ['admin'],  
        'Fetch admin users'  
    );  
    
    // Insert dengan logging  
    executeQuery(  
        "INSERT INTO user (nama, email, username, password) VALUES (?, ?, ?, ?)",  
        ['John Doe', 'john@example.com', 'johndoe', password_hash('password123', PASSWORD_DEFAULT)],  
        'Create new user'  
    );  
    
} catch (PDOException $e) {  
    // Error sudah di-log di fungsi  
    echo "Terjadi kesalahan: " . $e->getMessage();  
}  
*/  
?>