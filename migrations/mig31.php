<?php 

declare(strict_types=1);

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$pdo = new PDO('mysql:host='.$_ENV["DB_HOST"].';dbname='. $_ENV["DB_NAME"], $_ENV["DB_USER"], $_ENV["DB_PASSWORD"]);
$stmt=$pdo->prepare(query:"CREATE table tasks(id INT PRIMARY KEY  auto_increment, task varchar(50), status tinyint(1));");
$stmt->execute();
printf("Created successsfully (Table 'tasks')!\n");

$pdo = new PDO('mysql:host='.$_ENV["DB_HOST"].';dbname='. $_ENV["DB_NAME"], $_ENV["DB_USER"], $_ENV["DB_PASSWORD"]);
$stmt=$pdo->prepare(query:"CREATE table command(query varchar(64), id int);");
$stmt->execute();
printf("Created successsfully (Table 'command')!\n");

$pdo = new PDO('mysql:host='.$_ENV["DB_HOST"].';dbname='. $_ENV["DB_NAME"], $_ENV["DB_USER"], $_ENV["DB_PASSWORD"]);
$stmt=$pdo->prepare(query:"INSERT INTO command(query,id) values('query', 1);");
$stmt->execute();
printf("Inserted successsfully (Table 'command')!\n");

?>