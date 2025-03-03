<?php

class User{
    public static string $name;

    public static function nName(string $name): string{
        return "$name";
    }
}

// var_dump(User::nName("Shuhrat"));
$a=new User();
var_dump($a->$name);

?>