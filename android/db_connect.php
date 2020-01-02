<?php
/** 
 * a class file to connect to database
 */
class DB_CONNECT
{
    public static $connection; //variable to hold the connection link
    //constructor
    function __construct()
    {
        //connecting to database
        $this->connect();
    }
    //destructor
    function __destruct()
    {
        $this->close();
    }
    /**
     * function to connect with database
     */
    function connect()
    {
        //import database connection vriables
        require_once __DIR__.'/db_config.php';
        //connecting to mysql database
        self::$connection= mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD,DB_DATABASE) or die(mysqli_connect_error());
        return self::$connection;
    }
    /**
     * function to close db connection
     */
    function close()
    {
        mysqli_close(self::$connection);
    }
}
?>