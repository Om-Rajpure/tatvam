<?php 

# Get all Author function
function get_all_author($con){
   $sql  = "SELECT * FROM authors";
   $stmt = $con->prepare($sql);
   $stmt->execute();

   if ($stmt->rowCount() > 0) {
   	  $authors = $stmt->fetchAll();
   }else {
      $authors = 0;
   }

   return $authors;
}


# Get  Author by ID function
function get_author($con, $id){
   $sql  = "SELECT * FROM authors WHERE id=?";
   $stmt = $con->prepare($sql);
   $stmt->execute([$id]);

   if ($stmt->rowCount() > 0) {
   	  $author = $stmt->fetch();
   }else {
      $author = 0;
   }

   return $author;
}

// Get author by user_id (for publish flow gate)
function get_author_by_user_id($conn, $user_id) {
    $sql = "SELECT * FROM authors WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$user_id]);
    if ($stmt->rowCount() > 0) {
        return $stmt->fetch();
    }
    return 0;
}

// Create author profile (self-registration from publish flow)
function create_author_profile($conn, $data) {
    $sql = "INSERT INTO authors (user_id, name, photo, about, qualification, designation, organization, contact)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute([
        $data['user_id'],
        $data['name'],
        $data['photo'] ?? null,
        $data['about'],
        $data['qualification'],
        $data['designation'] ?? null,
        $data['organization'] ?? null,
        $data['contact'] ?? null
    ]);
    if ($result) {
        return $conn->lastInsertId();
    }
    return 0;
}

// Update author profile
function update_author_profile($conn, $id, $data) {
    $photo_sql = isset($data['photo']) ? ", photo = ?" : "";
    $sql = "UPDATE authors SET name = ?, about = ?, qualification = ?, designation = ?,
            organization = ?, contact = ?" . $photo_sql . " WHERE id = ?";
    $params = [
        $data['name'],
        $data['about'],
        $data['qualification'],
        $data['designation'] ?? null,
        $data['organization'] ?? null,
        $data['contact'] ?? null
    ];
    if (isset($data['photo'])) {
        $params[] = $data['photo'];
    }
    $params[] = $id;
    $stmt = $conn->prepare($sql);
    return $stmt->execute($params);
}

// Get all authors with book count (for authors listing page)
function get_all_authors_with_stats($conn) {
    $sql = "SELECT a.*, COUNT(b.id) as book_count
            FROM authors a
            LEFT JOIN books b ON b.author_id = a.id
            GROUP BY a.id
            ORDER BY a.name ASC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        return $stmt->fetchAll();
    }
    return 0;
}

// Get books by author filtered by content_type
function get_books_by_author_typed($conn, $author_id, $type) {
    $sql = "SELECT * FROM books WHERE author_id = ? AND content_type = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$author_id, $type]);
    if ($stmt->rowCount() > 0) {
        return $stmt->fetchAll();
    }
    return 0;
}