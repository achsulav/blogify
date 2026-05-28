<?php
require_once __DIR__ . '/app/bootstrap.php';
$db = (new \App\Foundation\Application())->db->getConnection();

// Let's pretend the user selected Fitness (5), Lifestyle (3), Photography (26)
// Find their IDs first:
$stmt = $db->query("SELECT id, name FROM categories WHERE name IN ('Fitness', 'Lifestyle', 'Photography')");
$cats = $stmt->fetchAll(PDO::FETCH_ASSOC);
$ids = array_column($cats, 'id');
$inClause = implode(',', $ids);
echo "In Clause: $inClause\n";

$sql = "
  SELECT p.title, c.name as cat_name, p.views_count, p.created_at,
  IF(p.category_id IN ($inClause), 1, 0) as category_match,
  (
      IF(p.category_id IN ($inClause), 1, 0) * 0.7 +
      LEAST((p.views_count + ((SELECT COUNT(*) FROM post_likes WHERE post_id = p.id)*5))/1000, 1) * 0.15 +
      EXP(-DATEDIFF(NOW(), p.created_at)/30) * 0.10 +
      (LEAST(p.views_count/1000, 1) * EXP(-DATEDIFF(NOW(), p.created_at)/7)) * 0.05
  ) as final_score
  FROM posts p
  LEFT JOIN categories c ON p.category_id = c.id
  ORDER BY final_score DESC
  LIMIT 5
";
$stmt = $db->query($sql);
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
