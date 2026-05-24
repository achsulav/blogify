<?php
namespace App\Foundation;
use App\Foundation\Application;

abstract class Migration
{
  protected $db;
  public function __construct(){
    $this->db = Application::$app->db->getConnection();
  }
  abstract public function up();
  abstract public function down();
}
