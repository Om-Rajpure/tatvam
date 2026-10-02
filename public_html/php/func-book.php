<?php  

# Get All books function
function get_all_books($con){
   $sql  = "SELECT * FROM books ORDER bY id DESC";
   $stmt = $con->prepare($sql);
   $stmt->execute();

   if ($stmt->rowCount() > 0) {
   	  $books = $stmt->fetchAll();
   }else {
      $books = 0;
   }

   return $books;
}



# Get  book by ID function
function get_book($con, $id){
   $sql  = "SELECT * FROM books WHERE id=?";
   $stmt = $con->prepare($sql);
   $stmt->execute([$id]);

   if ($stmt->rowCount() > 0) {
   	  $book = $stmt->fetch();
   }else {
      $book = 0;
   }

   return $book;
}


# Search books function
function search_books($con, $key){
   # creating simple search algorithm :) 
   $key = "%{$key}%";

   $sql  = "SELECT * FROM books 
            WHERE title LIKE ?
            OR description LIKE ?";
   $stmt = $con->prepare($sql);
   $stmt->execute([$key, $key]);

   if ($stmt->rowCount() > 0) {
        $books = $stmt->fetchAll();
   }else {
      $books = 0;
   }

   return $books;
}

# get books by category
function get_books_by_category($con, $id){
   $sql  = "SELECT * FROM books WHERE category_id=?";
   $stmt = $con->prepare($sql);
   $stmt->execute([$id]);

   if ($stmt->rowCount() > 0) {
        $books = $stmt->fetchAll();
   }else {
      $books = 0;
   }

   return $books;
}


# get books by author
function get_books_by_author($con, $id){
   $sql  = "SELECT * FROM books WHERE author_id=?";
   $stmt = $con->prepare($sql);
   $stmt->execute([$id]);

   if ($stmt->rowCount() > 0) {
        $books = $stmt->fetchAll();
   }else {
      $books = 0;
   }

   return $books;
}

// Get books filtered by content_type
function get_books_by_type($conn, $type) {
    $sql = "SELECT * FROM books WHERE content_type = ? ORDER BY id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$type]);
    if ($stmt->rowCount() > 0) {
        return $stmt->fetchAll();
    }
    return 0;
}

// Get filtered publications with multi-facet support
function get_filtered_books($conn, $type = null, $category_id = null, $search = null, $sort = 'latest') {
    $sql = "SELECT b.*, c.name as category_name, a.name as author_name 
            FROM books b 
            LEFT JOIN categories c ON b.category_id = c.id 
            LEFT JOIN authors a ON b.author_id = a.id 
            WHERE 1=1";
    $params = [];

    if (!empty($type) && in_array($type, ['book', 'research_paper'])) {
        $sql .= " AND b.content_type = ?";
        $params[] = $type;
    }

    if (!empty($category_id) && $category_id > 0) {
        $sql .= " AND b.category_id = ?";
        $params[] = $category_id;
    }

    if (!empty($search)) {
        $sql .= " AND (b.title LIKE ? OR b.description LIKE ? OR a.name LIKE ?)";
        $searchParam = "%{$search}%";
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
    }

    switch ($sort) {
        case 'price_asc':
            $sql .= " ORDER BY b.price ASC, b.id DESC";
            break;
        case 'price_desc':
            $sql .= " ORDER BY b.price DESC, b.id DESC";
            break;
        case 'title_asc':
            $sql .= " ORDER BY b.title ASC";
            break;
        case 'latest':
        default:
            $sql .= " ORDER BY b.id DESC";
            break;
    }

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    if ($stmt->rowCount() > 0) {
        return $stmt->fetchAll();
    }
    return 0;
}

// Get counts for tabs
function get_content_counts($conn) {
    $sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN content_type = 'book' THEN 1 ELSE 0 END) as books_count,
                SUM(CASE WHEN content_type = 'research_paper' THEN 1 ELSE 0 END) as papers_count
            FROM books";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetch() ?: ['total' => 0, 'books_count' => 0, 'papers_count' => 0];
}