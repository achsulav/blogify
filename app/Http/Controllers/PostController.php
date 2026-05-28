<?php
namespace App\Http\Controllers;
use App\Foundation\Application;
class PostController extends BaseController
{
  public function create()
  {
    $db= Application::$app->db->getConnection();
    $stmt = $db->prepare("SELECT * FROM categories ORDER BY name ASC");
    $stmt->execute();
    $categories= $stmt->fetchAll();

    $old = Application::$app->session->get('old');
    Application::$app->session->remove('old');
    $this->render('CreatePost',[
      'categories'=>$categories,
      'old' => $old
    ]);
  }
  public function show($slug)
  {
    $subdomain = Application::$app->getSubdomain();
    $db = Application::$app->db->getConnection();
    
    $blogOwner = null;
    $ownerId = null;

    if ($subdomain && $subdomain !== 'blogify') {
        // Find owner by username (subdomain)
        $userStmt = $db->prepare("SELECT id, name FROM users WHERE username = :username LIMIT 1");
        $userStmt->execute([':username' => $subdomain]);
        $owner = $userStmt->fetch();
        
        if (!$owner) {
            http_response_code(404);
            $this->render('BlogNotFound', [
                'subdomain' => $subdomain,
                'blogOwner' => null
            ]);
            exit;
        }
        $blogOwner = $owner['name'];
        $ownerId = $owner['id'];
    }

    $sql = "SELECT posts.*, users.name AS author FROM posts JOIN users ON posts.user_id = users.id WHERE posts.slug = :slug";
    $params = [':slug' => $slug];
    
    if ($ownerId) {
        $sql .= " AND posts.user_id = :owner_id";
        $params[':owner_id'] = $ownerId;
    }
    
    $sql .= " LIMIT 1";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $post = $stmt->fetch();

    if (!$post) {
      // If no subdomain, we might be looking for a user profile via /{username}
      if (!$subdomain || $subdomain === 'blogify') {
          $userStmt = $db->prepare("SELECT id, username FROM users WHERE username = :username LIMIT 1");
          $userStmt->execute([':username' => $slug]);
          $potentialUser = $userStmt->fetch();
          if ($potentialUser) {
            $userController = new UserController();
            return $userController->profile($potentialUser['username']);
          }
      }

      http_response_code(404);
      if ($subdomain && $subdomain !== 'blogify') {
          $this->render('BlogNotFound', [
              'subdomain' => $subdomain,
              'blogOwner' => null // Explicitly hide owner name in header for 404
          ]);
      } else {
          $this->render('PageNotFound');
      }
      exit;
    }

    $commentsql = "SELECT comments.*, users.name AS user_name FROM comments JOIN users ON comments.user_id = users.id WHERE comments.post_id = :post_id ORDER BY comments.created_at DESC";
    $commentStmt = $db->prepare($commentsql);
    $commentStmt->execute([':post_id'=> $post['id']]);
    $comments = $commentStmt->fetchAll();
    
    if (!$blogOwner) {
        $blogOwner = $post['author'] ?? null;
    }

    $this->render('ViewPost', [
      'post' => $post,
      'comments' => $comments,
      'blogOwner' => $blogOwner
    ]);
  }
  public function store(){
    $title = $_POST['title'] ?? '';
    $html = $_POST['content_html'] ?? '';
      Application::$app->session->set('old',[
        'title' => $title,
        'content_html' => $html,
      ]);
    $slug = $this->generateSlug($title);
    $userID = Application::$app->session->get('user');
    $categoryId = $_POST['category_id'] ?? null;
    $sql = "INSERT INTO posts (user_id, category_id, title, slug, content_html) VALUES (?, ?, ?, ?,?)";
    $stmt = Application::$app->db->getConnection()->prepare($sql);
    $stmt->execute([
       $userID,
       $categoryId,
       $title,
      $slug,
      $html,
 ]);
    if(!$stmt->rowCount()){
      Application::$app->session->setFlash('error', 'Failed to create post');
      $this->redirect('/create-post');
      return;
    }
Application::$app->session->setFlash('success', 'Post created successfully');
$this->redirect('/dashboard');
  }

  // ===== UPLOAD IMAGE =====
  public function uploadImage()
  {
    if (!isset($_FILES['image'])) {
      http_response_code(400);
      echo  json_encode(['error'=> 'No file']);
      $this->uploadError('No file uploaded.');
      return;
    }

    $file = $_FILES['image'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
      http_response_code(400);
      echo  json_encode(['error'=> 'Upload Error']);
      $this->uploadError('Upload failed.');
      return;
    }

    if ($file['size'] > 2 * 1024 * 1024) {
      $this->uploadError('File too large. Max 2MB allowed.');
      return;
    }
    $allowTypes = ['image/jpeg', 'image/png', 'image/webp'];
      if (!in_array($file['type'], $allowTypes)) {
        http_response_code(400);
        echo  json_encode(['error'=> 'Invalid file type']);
        $this->uploadError('Invalid file type. Only JPG, PNG, and WEBP allowed.');
        return;
      }

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($extension, $allowedExtensions)) {
      $this->uploadError('Invalid file type.');
      return;
    }

    $imageInfo = getimagesize($file['tmp_name']);
    if ($imageInfo === false) {
      $this->uploadError('Invalid image file.');
      return;
    }

    $userId = Application::$app->session->get('user');

    $uploadDir = __DIR__ . '/../../../public/uploads/posts/' . $userId;

    if (!is_dir($uploadDir)) {
      mkdir($uploadDir, 0755, true);
    }

    $filename = uniqid('img_', true) . '.' . $extension;
    $destination = $uploadDir . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
      $this->uploadError('Failed to save file.');
      return;
    }

    $url = "/uploads/posts/{$userId}/{$filename}";
    header('Content-Type: application/json');
    echo json_encode([
      'status' => 'success',
      'url' => $url
    ]);
    exit;
  }
  // ===== UPLOAD ERROR HANDLER =====
  public function edit($id){
    $userId = Application::$app->session->get('user');
    $db = Application::$app->db->getConnection();
    $sql = "SELECT * FROM posts where id = :id AND user_id = :user_id LIMIT 1";
    $stmt = $db->prepare($sql);

    $stmt->execute([
      ':id'=> $id,
      ':user_id'=>$userId
    ]);
    $post = $stmt->fetch();
    if(!$post){
      http_response_code(404);
      $this->render('PageNotFound', ['blogOwner' => null]);
      return;
    }

    $catStmt = $db->prepare("SELECT * FROM categories ORDER BY name ASC");
    $catStmt->execute();
    $categories = $catStmt->fetchAll();

    $this->render('EditPost',[
      'post'=> $post,
      'categories' => $categories
    ]);

  }
  public function update($id){
    $userId = Application::$app->session->get('user');
    $title = trim($_POST['title'] ?? '');
    $content = $_POST['content_html'] ?? '';
    $plainText = trim(strip_tags($content));

    if(empty($title) || empty($plainText)){
      Application::$app->session->setFlash('error', 'Title and content are required');     
      $this->redirect("/post/edit/$id");
      return;
    }
    $sql = "SELECT * FROM posts WHERE id = :id AND user_id = :user_id LIMIT 1";
    $stmt = Application::$app->db->getConnection()->prepare($sql);
    $stmt->execute([
      ':id' => $id,
      ':user_id'=> $userId
    ]);
    $post = $stmt->fetch();
    if(!$post){
      http_response_code(403);
      exit('Unauthorized');
    }

    $slug = $post['slug'];
    if($post['title'] !== $title){
      $slug = $this->generateSlug($title);
    }
    $categoryId = $_POST['category_id'] ?? null;
    $updateSql = "UPDATE posts SET title = :title,slug = :slug,content_html = :content_html, category_id = :category_id WHERE id = :id AND user_id = :user_id";
    $updateStmt = Application::$app->db->getConnection()->prepare($updateSql);
    $updateStmt->execute([
      ':title'=>$title,
      ':slug'=>$slug,
      ':content_html' => $content,
      ':category_id' => $categoryId,
      ':id'=>$id,
      ':user_id'=>$userId
    ]);
    Application::$app->session->setFlash('success', 'Post updated Successfully');
    $this->redirect('/dashboard');
  }
  public function delete(){
    $postId = $_POST['post_id'] ?? null;
    $userId = Application::$app->session->get('user');
    if(!$postId){
      Application::$app->session->setFlash('error', 'Invalid request');
      $this->redirect('/dashboard');
      return;
    }

    $sql = "SELECT * FROM posts WHERE id = :id AND user_id = :user_id LIMIT 1";
    $stmt = Application::$app->db->getConnection()->prepare($sql);
    $stmt->execute([
      ':id' => $postId,
      ':user_id'=> $userId
    ]);
    $post = $stmt->fetch();
    if(!$post){
      http_response_code(403);
      exit('Unauthorized');
      $this->redirect('/dashboard');
      return;
    }
    $deleteSql = "DELETE FROM posts where id = :id AND user_id = :user_id";
    $deleteStmt = Application::$app->db->getConnection()->prepare($deleteSql);
    $deleteStmt->execute([
      ':id'=>$postId,
      ':user_id'=>$userId
    ]);
    Application::$app->session->setFlash('success', 'Post delete Successfully');
    $this->redirect('/dashboard');
  }
  

  public function index(){
    $db = Application::$app->db->getConnection();
    $limit = 5;
    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    $page = max($page,1);
    $search = isset($_GET['search']) ? trim($_GET['search']) : ''; 
    $categorySlug = $_GET['category'] ?? '';
    $offset = ($page - 1) * $limit;

    $where = "WHERE 1=1";
    $params = [];
    
    // Personalized Feed Logic
    $userId = Application::$app->session->get('user');
    $userInterests = [];
    if ($userId && empty($categorySlug) && empty($search)) {
        $userModel = new \App\Models\User(Application::$app->db);
        $interests = $userModel->getInterests($userId);
        $userInterests = array_column($interests, 'id');
    }
    $subdomain = Application::$app->getSubdomain();
    $blogOwner = null;
    if ($subdomain && $subdomain !== 'blogify') {
      $userStmt = $db->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
      $userStmt->execute([':username' => $subdomain]);
      $owner = $userStmt->fetch();
      if ($owner) {
        $blogOwner = $owner['name'];
        $where .= " AND posts.user_id = :owner_id";
        $params[':owner_id'] = $owner['id'];
      }
    }

    if (!empty($search)) {
      $where .= " AND (posts.title LIKE :search OR posts.content_html LIKE :search)";
      $params[':search'] = '%' . $search . '%';
    }
    if(!empty($categorySlug)){
      $where .= " AND categories.slug = :category"; 
      $params[':category'] = $categorySlug;
    }
    $countSql = "SELECT COUNT(*) FROM posts JOIN users ON posts.user_id = users.id LEFT JOIN categories ON posts.category_id = categories.id $where";

    $countStmt = $db->prepare($countSql);
    $countStmt->execute($params);
    $totalPosts = $countStmt->fetchColumn();
    $totalPages = ceil($totalPosts/$limit);

    if($page > $totalPages && $totalPages > 0 ){
      $page = $totalPages;
      $offset = ($page - 1) * $limit;
    }
    $interestOrder = "";
    if (!empty($userInterests)) {
        // Safe because values are ints from DB
        $inClause = implode(',', array_map('intval', $userInterests));
        $interestOrder = "CASE WHEN posts.category_id IN ($inClause) THEN 1 ELSE 0 END DESC, ";
    }

    $sql = "SELECT posts.*, users.name AS author, users.username, categories.name AS category_name FROM posts JOIN users ON posts.user_id = users.id LEFT JOIN categories ON posts.category_id = categories.id $where ORDER BY $interestOrder posts.created_at DESC LIMIT :limit OFFSET :offset";
    $stmt = $db->prepare($sql);

    if(!empty($search)){
      $stmt->bindValue(':search','%' . $search . '%');
    }
    foreach($params as $key => $value){
      if($key === ':search') continue;
      $stmt->bindValue($key,$value);
    }
    
    $stmt->bindValue(':limit',$limit, \PDO::PARAM_INT);
    $stmt->bindValue(':offset',$offset, \PDO::PARAM_INT);
    $stmt->execute();
    $posts = $stmt->fetchAll();
    $this->render('BlogIndex',[
      'posts'=> $posts,
      'currentPage'=>$page,
      'totalPages'=>$totalPages,
      'search'=> $search,
      'category' => $categorySlug,
      'blogOwner' => null
    ], 'clean');
  }

  private function generateSlug(string $title):string
  {
    $baseSlug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    $baseSlug = trim($baseSlug, '-');
    $userId = Application::$app->session->get('user');
    $slug = $baseSlug;
    $counter = 1;
    $db = Application::$app->db->getConnection();
    while(true){
      $sql = "SELECT COUNT(*) FROM posts WHERE slug = :slug AND user_id = :user_id";
      $stmt = $db->prepare($sql);
      $stmt->execute([':slug' => $slug, ':user_id' => $userId]);
      $exists = $stmt->fetchColumn();
      if(!$exists){
        break;
      }
      $slug = $baseSlug . '-' . $counter;
      $counter++;
    }
    return $slug;
  }

  private function uploadError(string $message)
  {
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode(['error' => $message]);
    exit;
  }
}
