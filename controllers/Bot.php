<?php
declare(strict_types=1);

require 'vendor/autoload.php';

use GuzzleHttp\Client;

class Bot {
  public  string $text;
  public  int    $chatId;
  public  string $firstName;

  private string $api;
  private        $http;

  public function __construct(string $token){
    $this->api  = "https://api.telegram.org/bot$token/"; // $token got from credentials.php // FIXME: Replace with .env
    $this->http = new Client(['base_uri' => $this->api]);
  }

  public function handle(string $update){
    $update = json_decode($update);

    $this->text      = $update->message->text;
    $this->chatId    = $update->message->chat->id;
    $this->firstName = $update->message->chat->first_name;

    $called_query=(new Todo())->getQuery();

    match($this->text){
      '/start' => $this->handleStartCommand(),
      '/list'  => $this->handleListCommand(),
      '/add'   => $this->startAddCommand(),
      'Done'   =>$this->handleStatus(true),
      'Undone' => $this->handleStatus(false),
      default  => $this->prepareCommand($this->text, $called_query),
      
    };
  }

  public function handleStartCommand(){
      $text = "Assalomu alaykum $this->firstName";
      $text .= "\n\nBotimizga xush kelibsiz!";
      $text .= "\n\nBotdan foydalanish uchun quyidagi buyruqlardan birini tanlang:";
      $text .= "\n\n/list - Bor tasklar ro'yxati";
      $text .= "\n/add - Task qo'shish";
      $text .= "\n/delete - Taskni o'chirish";
      $text .= "\n/update - Taskni bajarilgan qilib belgilash";
      
      $this->http->post('sendMessage', [
        'form_params' => [
            'chat_id' => $this->chatId,
            'text'    => $text
        ]
      ]); 
  }

  // public function handleReply(){
  //   $text = "Tasdiqlandi !";
  //   $text .= "\n\n/list - Bor tasklar ro'yxati";
  //   $text .= "\n/add - Task qo'shish";
  //   $text .= "\n/delete - Taskni o'chirish";
  //   $text .= "\n/done - Taskni bajarilgan qilib belgilash";
  //   $text .= "\n/undone - Taskni bajarilmagan qilib belgilash";
    
  //   $this->http->post('sendMessage', [
  //     'form_params' => [
  //         'chat_id' => $this->chatId,
  //         'text'    => $text
  //     ]
  //   ]);
  // }

  public function handleListCommand(){
    $tasks = (new Todo())->getTasks();

    $this->http->post('sendMessage', [
        'form_params' => [
            'chat_id' => $this->chatId,
            'text'    => print_r($tasks, true)
        ]
      ]);

  }

  public function setWebhook(string $url): string {
    try{
      $response = $this->http->post('setWebhook', [
        'form_params' => [
          'url'                  => $url,
          'drop_pending_updates' => true
        ]
      ]);

      $response = json_decode($response->getBody()->getContents());
    
      return $response->description;
    } catch(Exception $e){
      return $e->getMessage();
    }
  }

  public function startAddCommand(){

    $todo=new Todo();
    $todo->setQuery("Yangi taskni kiriting :");

    $this->http->post('sendMessage', [
        'form_params' => [
            'chat_id' => $this->chatId,
            'text'    => "Yangi taskni kiriting :"
        ]
      ]);

  }

  public function handleAddCommand($task){
    
    $todo = new Todo();
    $todo->addTask($task);

    $this->http->post('sendMessage', [
      'form_params' => [
          'chat_id' => $this->chatId,
          'text'    => "Task status ? :",
          'reply_markup' => json_encode([
          'keyboard' => [
            [
              ['text' => 'Done'],
              ['text' => 'Undone'],
            ]
          ]
        ])
      ]
    ]);
  }
  
  public function handleStatus($status){
    
    $todo = new Todo();
    $todo->addStatus($status);
    
    $text = "Tasdiqlandi !";
    $text .= "\n\n/list - Bor tasklar ro'yxati";
    $text .= "\n/add - Task qo'shish";
    $text .= "\n/delete - Taskni o'chirish";
    $text .= "\n/done - Taskni bajarilgan qilib belgilash";
    $text .= "\n/undone - Taskni bajarilmagan qilib belgilash";
    
    $this->http->post('sendMessage', [
      'form_params' => [
          'chat_id' => $this->chatId,
          'text'    => $text
      ]
    ]);
  }

  public function prepareCommand($given_task, $called_query) {
    if ($given_task && $called_query === "Yangi taskni kiriting :") {

        return $this->handleAddCommand($given_task);
    }
}

}
