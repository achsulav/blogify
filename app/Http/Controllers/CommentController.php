<?php
namespace App\Http\Controllers;
use App\Foundation\Application; 

class CommentController extends BaseController{
  public function update(){
    header('Content-Type: application/json');
    $userId = Application::$app->session->get('user');
    $commentId = $_POST['comment_id'] ?? null;
    $content = trim($_POST['content'] ?? '');

    if(!$userId || !$commentId){
      echo json_encode(['status'=> 'error','message'=>'Invalid request']);
      return;
    }
    if(empty($content)){
      echo json_encode(['status'=>'error','message'=>'Comment cannot be empty']);
      return;
    }
    $sql = "SELECT * FROM comments WHERE id = :id AND user_id = :user_id LIMIT 1";
    $stmt = Application::$app->db->getConnection()->prepare($sql);
    $stmt->execute([
      ':id'=>$commentId,
      ':user_id'=> $userId
    ]);
    $comment = $stmt->fetch();
    if(!$comment){
      echo json_encode(['status'=>'error','message'=>'Unauthorized']);
      return;
    }
    $updateSql = "UPDATE comments SET content= :content WHERE id = :id";

    $updateStmt = Application::$app->db->getConnection()->prepare($updateSql);
    $updateStmt->execute([
      ':content'=>$content,
      ':id' => $commentId
    ]);
    echo json_encode(['status'=>'success','content'=>strip_tags($content, '<b><i><strong><em><p><br><u>')]);
  }
  public function delete(){
    header('Content-Type:application/json');
    
    $userId = Application::$app->session->get('user');
    $commentId = $_POST['comment_id'] ?? null;


    if(!$userId || !$commentId){
      echo json_encode(['status'=> 'error','message'=>'Invalid request']);
      return;
    }

    $sql = "SELECT * FROM comments WHERE id = :id AND user_id = :user_id LIMIT 1";
    $stmt = Application::$app->db->getConnection()->prepare($sql);
    $stmt->execute([
      ':id'=>$commentId,
      ':user_id'=> $userId
    ]);
    $comment = $stmt->fetch();
    if(!$comment){
      echo json_encode(['status'=> 'error','message'=>'Unauthorized']);
      http_response_code(403);
        exit('Unauthorized');
    }
    $deleteSql = "DELETE FROM comments WHERE id = :id";
    $deleteStmt = Application::$app->db->getConnection()->prepare($deleteSql);
    $deleteStmt->execute([
      ':id' => $commentId
    ]);
    echo json_encode(['status'=> 'success']);
  }
  public function store(){
    header('Content-Type:application/json');
    $userId = Application::$app->session->get('user');
    if(!$userId){
      echo json_encode(['status'=> 'error','message'=> 'Login Required']);
      return;
    }
    $postId = $_POST['post_id'] ?? null;
    $content = trim($_POST['content'] ?? '');
    if(empty($content)){
      echo json_encode(['status'=> 'error','message'=> "Comment cannot be empty"]);
      return;
    }
    
    $sql = "INSERT INTO comments(post_id,user_id,content) VALUES(:post_id,:user_id,:content)";
    $stmt = Application::$app->db->getConnection()->prepare($sql);
    $stmt->execute([
      ':post_id' => $postId,
      ':user_id' => $userId,
      ':content' => $content
    ]);
    $commentId = Application::$app->db->getConnection()->lastInsertId();
    $userSql = "SELECT name FROM users WHERE id = :id";
    $userStmt = Application::$app->db->getConnection()->prepare($userSql);
    $userStmt->execute([':id'=> $userId]);
    $user = $userStmt->fetch();
    echo json_encode([
      'status' => 'success',
      'comment' => [
        'id' => $commentId,
        'user_name' => $user['name'],
        'content' => strip_tags($content, '<b><i><strong><em><p><br><u>'),
        'created_at'=> date('Y-m-d H:i:s'),
        'user_id'=> $userId

      ]
    ]);

  }
}
