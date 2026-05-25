<?php
session_start();
include("../db.php");

if (isset($_POST['btn_save'])) {

    // Sanitise text inputs
    $product_name = trim(mysqli_real_escape_string($con, $_POST['product_name']));
    $details      = trim(mysqli_real_escape_string($con, $_POST['details']));
    $price        = trim(mysqli_real_escape_string($con, $_POST['price']));
    $product_type = (int) $_POST['product_type'];   // cast to int – safe for SQL
    $brand        = (int) $_POST['brand'];           // cast to int – safe for SQL
    $tags         = trim(mysqli_real_escape_string($con, $_POST['tags']));

    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $allowed_exts  = ['jpg', 'jpeg', 'png', 'gif'];
    $max_size      = 5 * 1024 * 1024; // 5 MB

    $picture_name     = $_FILES['picture']['name'];
    $picture_type     = $_FILES['picture']['type'];
    $picture_tmp_name = $_FILES['picture']['tmp_name'];
    $picture_size     = $_FILES['picture']['size'];
    $picture_error    = $_FILES['picture']['error'];

    $file_ext = strtolower(pathinfo($picture_name, PATHINFO_EXTENSION));

    if ($picture_error !== UPLOAD_ERR_OK) {
        $error = "File upload error. Please try again.";

    } elseif (!in_array($picture_type, $allowed_types) || !in_array($file_ext, $allowed_exts)) {
        $error = "Invalid file type. Only JPG, PNG, and GIF are allowed.";

    } elseif ($picture_size > $max_size) {
        $error = "File is too large. Maximum allowed size is 5 MB.";

    } else {
        // Generate a safe, unique filename (no original name to avoid path traversal)
        $pic_name   = time() . '_' . bin2hex(random_bytes(8)) . '.' . $file_ext;
        $upload_dir = "../product_images/";

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        if (move_uploaded_file($picture_tmp_name, $upload_dir . $pic_name)) {

            // ── Prepared statement – prevents SQL injection ──────────────────
            $stmt = mysqli_prepare(
                $con,
                "INSERT INTO products
                    (product_cat, product_brand, product_title, product_price,
                     product_desc, product_image, product_keywords)
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );

            if ($stmt) {
                mysqli_stmt_bind_param(
                    $stmt, "iisssss",
                    $product_type, $brand, $product_name,
                    $price, $details, $pic_name, $tags
                );

                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_close($stmt);
                    mysqli_close($con);
                    header("location: sumit_form.php?success=1");
                    exit; // Always exit after redirect
                } else {
                    $error = "Database error: " . mysqli_stmt_error($stmt);
                    mysqli_stmt_close($stmt);
                }
            } else {
                $error = "Failed to prepare statement: " . mysqli_error($con);
            }

        } else {
            $error = "Failed to move uploaded file. Check folder permissions.";
        }
    }

    mysqli_close($con);
}

include "sidenav.php";
include "topheader.php";
?>

<!-- End Navbar -->
<div class="content">
  <div class="container-fluid">

    <?php if ($success): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Success!</strong> Product has been added successfully.
        <button type="button" class="close" data-dismiss="alert">&times;</button>
      </div>
    <?php endif; ?>

    <?php if ($error): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Error!</strong> <?php echo htmlspecialchars($error); ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
      </div>
    <?php endif; ?>

    <form action="" method="post" name="form" enctype="multipart/form-data">
      <div class="row">

        <!-- ── Left card: Product Details ─────────────────────────────────── -->
        <div class="col-md-7">
          <div class="card">
            <div class="card-header card-header-primary">
              <h5 class="title">Add Product</h5>
            </div>
            <div class="card-body">
              <div class="row">

                <div class="col-md-12">
                  <div class="form-group">
                    <label for="product_name">Product Title</label>
                    <input type="text" id="product_name" name="product_name"
                           class="form-control" required
                           value="<?php echo isset($_POST['product_name']) ? htmlspecialchars($_POST['product_name']) : ''; ?>">
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <label for="picture">Add Image</label>
                    <input type="file" name="picture" id="picture"
                           accept=".jpg,.jpeg,.png,.gif"
                           class="btn btn-fill btn-success" required>
                    <small class="text-muted">Max 5 MB. JPG, PNG, GIF only.</small>
                  </div>
                </div>

                <div class="col-md-12">
                  <div class="form-group">
                    <label for="details">Description</label>
                    <textarea rows="4" cols="80" id="details" name="details"
                              class="form-control" required><?php
                      echo isset($_POST['details']) ? htmlspecialchars($_POST['details']) : '';
                    ?></textarea>
                  </div>
                </div>

                <div class="col-md-12">
                  <div class="form-group">
                    <label for="price">Pricing</label>
                    <input type="number" step="0.01" min="0" id="price" name="price"
                           class="form-control" required
                           value="<?php echo isset($_POST['price']) ? htmlspecialchars($_POST['price']) : ''; ?>">
                  </div>
                </div>

              </div><!-- /.row -->
            </div><!-- /.card-body -->
          </div><!-- /.card -->
        </div><!-- /.col-md-7 -->

        <!-- ── Right card: Categories ─────────────────────────────────────── -->
        <div class="col-md-5">
          <div class="card">
            <div class="card-header card-header-primary">
              <h5 class="title">Categories</h5>
            </div>
            <div class="card-body">
              <div class="row">

                <div class="col-md-12">
                  <div class="form-group">
                    <label for="product_type">Product Category</label>
                    <input type="number" id="product_type" name="product_type"
                           class="form-control" required
                           min="1" max="6" pattern="[1-6]"
                           value="<?php echo isset($_POST['product_type']) ? (int)$_POST['product_type'] : ''; ?>">
                    <small class="text-muted">Enter a value between 1 and 6.</small>
                  </div>
                </div>

                <div class="col-md-12">
                  <div class="form-group">
                    <label for="brand">Product Brand</label>
                    <input type="number" id="brand" name="brand"
                           class="form-control" required min="1"
                           value="<?php echo isset($_POST['brand']) ? (int)$_POST['brand'] : ''; ?>">
                  </div>
                </div>

                <div class="col-md-12">
                  <div class="form-group">
                    <label for="tags">Product Keywords</label>
                    <input type="text" id="tags" name="tags"
                           class="form-control" required
                           value="<?php echo isset($_POST['tags']) ? htmlspecialchars($_POST['tags']) : ''; ?>">
                  </div>
                </div>

              </div><!-- /.row -->
            </div><!-- /.card-body -->

            <div class="card-footer">
              <button type="submit" id="btn_save" name="btn_save"
                      class="btn btn-fill btn-primary">
                Add Product
              </button>
            </div>
          </div><!-- /.card -->
        </div><!-- /.col-md-5 -->

      </div><!-- /.row -->
    </form>

  </div><!-- /.container-fluid -->
</div><!-- /.content -->

<?php include "footer.php"; ?>