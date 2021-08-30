<?php
class Db
{
    public static function connect() 
    {
        $connect = mysqli_connect("localhost", "vicezhik_test2", "qCRb9ka3", "vicezhik_test2") or die("Couldn't connect");
        $connect->set_charset("utf8");
        return $connect;
    }
}