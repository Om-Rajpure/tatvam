<?php  

# Get all Categories function
function get_all_categories($con){
   $sql  = "SELECT * FROM categories";
   $stmt = $con->prepare($sql);
   $stmt->execute();

   if ($stmt->rowCount() > 0) {
   	  $categories = $stmt->fetchAll();
   }else {
      $categories = 0;
   }

   return $categories;
}


# Get category by ID
function get_category($con, $id){
   $sql  = "SELECT * FROM categories WHERE id=?";
   $stmt = $con->prepare($sql);
   $stmt->execute([$id]);

   if ($stmt->rowCount() > 0) {
   	  $category = $stmt->fetch();
   }else {
      $category = 0;
   }

   return $category;
}

// Get all categories with publication count
function get_all_categories_with_count($con) {
   $sql  = "SELECT c.*, COUNT(b.id) as book_count 
            FROM categories c 
            LEFT JOIN books b ON c.id = b.category_id 
            GROUP BY c.id, c.name 
            ORDER BY c.name ASC";
   $stmt = $con->prepare($sql);
   $stmt->execute();

   if ($stmt->rowCount() > 0) {
       return $stmt->fetchAll();
   }
   return 0;
}