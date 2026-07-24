<?php  
session_start();

# If the admin is logged in
if (isset($_SESSION['user_id']) &&
    isset($_SESSION['user_email']) &&
    isset($_SESSION['user_type']) &&
    $_SESSION['user_type'] == 'admin') {

	# Database Connection File
	include "db_conn.php";

	# Book helper function
	include "php/func-book.php";
    $books = get_all_books($conn);

    # author helper function
	include "php/func-author.php";
    $authors = get_all_author($conn);

    # Category helper function
	include "php/func-category.php";
    $categories = get_all_categories($conn);

    # Book requests helper function
	include "php/func-book-request.php";
    $pending_requests = get_all_requests($conn, 'pending');
    $request_stats = get_request_stats($conn);

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>ADMIN</title>

    <!-- bootstrap 5 CDN-->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
	<link rel="stylesheet" href="css/admin-style.css">

    <!-- bootstrap 5 Js bundle CDN-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ" crossorigin="anonymous"></script>

</head>
<body>
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-2 admin-sidebar p-0">
				<div class="sidebar-header">
					<h4><i class="bi bi-shield-check"></i> Admin</h4>
				</div>
				<nav class="nav flex-column">
					<a class="nav-link active" href="admin.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
					<a class="nav-link" href="add-book.php"><i class="bi bi-book"></i> Add Book</a>
					<a class="nav-link" href="add-category.php"><i class="bi bi-folder"></i> Add Category</a>
					<a class="nav-link" href="add-author.php"><i class="bi bi-person"></i> Add Author</a>
					<a class="nav-link" href="admin-orders.php"><i class="bi bi-bag-check"></i> Orders</a>
					<a class="nav-link" href="admin-verify-payments.php"><i class="bi bi-credit-card-2-front"></i> Verify Payments</a>
					<a class="nav-link" href="admin-book-requests.php"><i class="bi bi-file-earmark-text"></i> Book Requests</a>
					<a class="nav-link" href="index.php"><i class="bi bi-house"></i> Store</a>
					<a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
				</nav>
			</div>

			<div class="col-md-10 admin-content p-4">
				<h2 class="mb-4"><i class="bi bi-speedometer2"></i> Dashboard</h2>
        <?php if (isset($_GET['error'])) { ?>
          <div class="alert alert-danger" role="alert">
			  <?=htmlspecialchars($_GET['error']); ?>
		  </div>
		<?php } ?>
		<?php if (isset($_GET['success'])) { ?>
          <div class="alert alert-success" role="alert">
			  <?=htmlspecialchars($_GET['success']); ?>
		  </div>
		<?php } ?>

		<!-- Book Requests Section -->
		<?php if (!empty($pending_requests)) { ?>
		<div class="card table-card border-0 mb-4">
			<div class="card-header bg-warning text-dark">
				<h5 class="mb-0"><i class="bi bi-file-earmark-text"></i> Pending Book Requests (<?= count($pending_requests) ?>)</h5>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-hover">
						<thead>
							<tr>
								<th>Cover</th>
								<th>Title</th>
								<th>Author</th>
								<th>Submitted By</th>
								<th>Date</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($pending_requests as $request): ?>
							<tr>
								<td><img src="uploads/cover/<?= $request['cover'] ?>" width="50" class="rounded"></td>
								<td><strong><?= htmlspecialchars($request['title']) ?></strong></td>
								<td><?= htmlspecialchars($request['author_name']) ?></td>
								<td><?= htmlspecialchars($request['full_name']) ?></td>
								<td><?= date('d M Y', strtotime($request['created_at'])) ?></td>
								<td>
									<a href="admin-book-requests.php" class="btn btn-sm btn-primary">
										<i class="bi bi-eye"></i> Review
									</a>
								</td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
				<a href="admin-book-requests.php" class="btn btn-warning">
					<i class="bi bi-list"></i> View All Requests
				</a>
			</div>
		</div>
		<?php } ?>


        <?php  if ($books == 0) { ?>
        	<div class="card table-card border-0 text-center py-5">
        		<div class="card-body">
        			<i class="bi bi-inbox display-1 text-muted mb-3"></i>
				<h4>No Books Available</h4>
			  	<p class="text-muted">There is no book in the database</p>
			</div>
		  </div>
        <?php }else {?>


        <!-- List of all books -->
		<h4 class="mb-3"><i class="bi bi-book"></i> All Books</h4>
		<div class="card table-card border-0">
			<div class="card-body">
				<div class="table-responsive">
		<table class="table table-hover">
			<thead>
				<tr>
					<th>#</th>
					<th>Title</th>
					<th>Author</th>
					<th>Description</th>
					<th>Price</th>
					<th>Category</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
			  <?php 
			  $i = 0;
			  foreach ($books as $book) {
			    $i++;
			  ?>
			  <tr>
				<td><?=$i?></td>
				<td>
					<img width="100"
					     src="uploads/cover/<?=$book['cover']?>" >
					<a  class="link-dark d-block
					           text-center"
					    href="uploads/files/<?=$book['file']?>">
					   <?=$book['title']?>	
					</a>
						
				</td>
				<td>
					<?php if ($authors == 0) {
						echo "Undefined";}else{ 

					    foreach ($authors as $author) {
					    	if ($author['id'] == $book['author_id']) {
					    		echo $author['name'];
					    	}
					    }
					}
					?>

				</td>
				<td><?=$book['description']?></td>
				<td><strong class="text-primary">₹<?=number_format($book['price'], 2)?></strong></td>
				<td>
					<?php if ($categories == 0) {
						echo "Undefined";}else{ 

					    foreach ($categories as $category) {
					    	if ($category['id'] == $book['category_id']) {
					    		echo $category['name'];
					    	}
					    }
					}
					?>
				</td>
				<td>
					<a href="edit-book.php?id=<?=$book['id']?>" 
					   class="btn btn-warning">
					   Edit</a>

					<a href="php/delete-book.php?id=<?=$book['id']?>" 
					   class="btn btn-danger">
				       Delete</a>
				</td>
			  </tr>
			  <?php } ?>
			</tbody>
		</table>
				</div>
			</div>
		</div>
	   <?php }?>

        <?php  if ($categories == 0) { ?>
        	<div class="card table-card border-0 text-center py-5 mt-4">
        		<div class="card-body">
        			<i class="bi bi-folder display-1 text-muted mb-3"></i>
				<h4>No Categories Available</h4>
			  	<p class="text-muted">There is no category in the database</p>
			</div>
		    </div>
        <?php }else {?>
	    <!-- List of all categories -->
		<h4 class="mt-5 mb-3"><i class="bi bi-folder"></i> All Categories</h4>
		<div class="card table-card border-0">
			<div class="card-body">
				<div class="table-responsive">
		<table class="table table-hover">
			<thead>
				<tr>
					<th>#</th>
					<th>Category Name</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<?php 
				$j = 0;
				foreach ($categories as $category ) {
				$j++;	
				?>
				<tr>
					<td><?=$j?></td>
					<td><?=$category['name']?></td>
					<td>
						<a href="edit-category.php?id=<?=$category['id']?>" 
						   class="btn btn-warning">
						   Edit</a>

						<a href="php/delete-category.php?id=<?=$category['id']?>" 
						   class="btn btn-danger">
					       Delete</a>
					</td>
				</tr>
			    <?php } ?>
			</tbody>
		</table>
				</div>
			</div>
		</div>
	    <?php } ?>

	    <?php  if ($authors == 0) { ?>
        	<div class="card table-card border-0 text-center py-5 mt-4">
        		<div class="card-body">
        			<i class="bi bi-person display-1 text-muted mb-3"></i>
				<h4>No Authors Available</h4>
			  	<p class="text-muted">There is no author in the database</p>
			</div>
		    </div>
        <?php }else {?>
	    <!-- List of all Authors -->
		<h4 class="mt-5 mb-3"><i class="bi bi-person"></i> All Authors</h4>
		<div class="card table-card border-0">
			<div class="card-body">
				<div class="table-responsive">
         <table class="table table-hover">
			<thead>
				<tr>
					<th>#</th>
					<th>Author Name</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<?php 
				$k = 0;
				foreach ($authors as $author ) {
				$k++;	
				?>
				<tr>
					<td><?=$k?></td>
					<td><?=$author['name']?></td>
					<td>
						<a href="edit-author.php?id=<?=$author['id']?>" 
						   class="btn btn-warning">
						   Edit</a>

						<a href="php/delete-author.php?id=<?=$author['id']?>" 
						   class="btn btn-danger">
					       Delete</a>
					</td>
				</tr>
			    <?php } ?>
			</tbody>
		</table> 
				</div>
			</div>
		</div>
		<?php } ?>
			</div>
		</div>
	</div>
</body>
</html>

<?php }else{
  header("Location: login.php");
  exit;
} ?>