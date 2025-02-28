<?php
declare(strict_types=1);

require_once __DIR__. '/../DB.php';

class Todo {
  private PDO $db;

  public function __construct(){
    $this->db = (new DB(
      $_ENV["DB_HOST"], 
      $_ENV["DB_NAME"], 
      $_ENV["DB_USER"], 
      $_ENV["DB_PASSWORD"]))->connect();
  }

  public function getTasks(): array {
    return $this->db->query("SELECT * FROM tasks")->fetchAll();
  }

  public function addTask(string $task): bool {
    $stmt = $this->db->prepare("INSERT INTO tasks (task) VALUES (:task)");
    
    return $stmt->execute([':task' => $task]);
  }
}
