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

  public function setQuery(string $query):void {
    $stmt = $this->db->prepare("UPDATE command SET query = :query;");
    
    $stmt->execute([':query' => $query]);
  }

  public function getQuery(): string {
    return $this->db->query("SELECT query FROM command")->fetch(PDO::FETCH_ASSOC)["query"];
  }

  public function addTask(string $task): void {
    $stmt = $this->db->prepare("INSERT INTO tasks (task) VALUES (:task)");
    
    $stmt->execute([':task' => $task]);
  }

  public function addStatus(bool $status): void {
    
    $stmt = $this->db->query("SELECT MAX(id) AS max_id FROM tasks");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $maxId = $result['max_id'];

    $stmt = $this->db->prepare("UPDATE tasks SET status = :status WHERE id = :id");
    $stmt->execute([':status' => (int)$status, ':id' => $maxId]);
  }

}
