<?php

class DB {
  public PDO $pdo;

  public function __construct(
    public string $host,
    public string $database,
    public string $username,
    public string $password,
  ){}

  public function connect(){
    try {
      $pdo = new PDO(
        "mysql:host=$this->host;dbname=$this->database",
        $this->username,
        $this->password);

      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

      return $pdo;
    } catch(PDOException $e) {
      file_put_contents('logs.txt', '\n'.$e->getMessage(), FILE_APPEND);
    }  
  }
}

