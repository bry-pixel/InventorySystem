<?php
$page_title = 'All Images';
require_once('includes/load.php');
page_require_level(2);

$media_files = find_all('media');

if (isset($_POST['submit'])) {
    $photo = new Media();
    $photo->upload($_FILES['file_upload']);
    if ($photo->process_media()) {
        $session->msg('s', 'Photo has been uploaded successfully.');
        log_activity(
            'Upload Photo',
            "Uploaded new photo: {$photo->file_name}",
            'media.php'
        );
    } else {
        $session->msg('d', join($photo->errors));
    }
    redirect('media.php');
}

include_once('layouts/header.php');
?>

<div class="row">
    <div class="col-md-12">
        <?php echo display_msg($msg); ?>
        <div class="panel panel-default">
            <div class="panel-heading clearfix">
                <h4 class="pull-left">
                    <span class="glyphicon glyphicon-camera"></span> All Photos
                </h4>
                <div class="pull-right">
                    <form class="form-inline" action="media.php" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <input type="file" name="file_upload" class="form-control" accept="image/*" required>
                        </div>
                        <button type="submit" name="submit" class="btn btn-success">
                            <i class="glyphicon glyphicon-upload"></i> Upload
                        </button>
                    </form>
                </div>
            </div>

            <div class="panel-body">
                <?php if (empty($media_files)): ?>
                    <div class="alert alert-info text-center">
                        <i class="glyphicon glyphicon-info-sign"></i> No images found.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered text-center">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Photo</th>
                                    <th>Photo Name</th>
                                    <th>Type</th>
                                    <th style="width: 80px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($media_files as $media_file): ?>
                                    <tr>
                                        <td><?php echo count_id(); ?></td>
                                        <td>
                                            <img src="uploads/products/<?php echo $media_file['file_name']; ?>" 
                                                 class="img-thumbnail preview-img" 
                                                 style="max-width: 80px; height: auto; cursor: pointer;" 
                                                 alt="<?php echo $media_file['file_name']; ?>">
                                        </td>
                                        <td><?php echo remove_junk($media_file['file_name']); ?></td>
                                        <td><?php echo remove_junk($media_file['file_type']); ?></td>
                                        <td>
                                            <a href="delete_media.php?id=<?php echo (int)$media_file['id']; ?>" 
                                               class="btn btn-danger btn-xs" 
                                               title="Delete" 
                                               onclick="return confirm('Are you sure you want to delete this photo?');">
                                                <span class="glyphicon glyphicon-trash"></span>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="text-center mt-3">
                        <nav aria-label="Transaction pagination">
                            <ul class="pagination pagination-sm" id="paginationControls" style="margin:0;"></ul>
                        </nav>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal for image preview -->
<div class="modal-overlay" id="imageModal">
    <div class="modal-content">
        <span class="modal-close" onclick="closeModal()">&times;</span>
        <img id="modalImage" src="" alt="Preview">
    </div>
</div>

<style>
/* Modal Overlay */
.modal-overlay {
  display: none;
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(0,0,0,0.8);
  z-index: 9999;
  overflow: auto;
}

/* Modal Content Box */
.modal-content {
  position: absolute;
  top: 50%; left: 50%;
  transform: translate(-50%, -50%);
  background: none;
  max-width: 90%;
  max-height: 90%;
  text-align: center;
}

/* Modal Image */
.modal-content img {
  max-width: 100%;
  max-height: 90vh;
  border-radius: 8px;
  box-shadow: 0 6px 18px rgba(0,0,0,0.5);
}

/* Close Button */
.modal-close {
  position: absolute;
  top: -12px; right: -12px;
    background: #ff4444;
  color: white;
  border-radius: 50%;
  font-size: 22px;
  width: 32px; height: 32px;
  text-align: center;
  line-height: 30px;
  cursor: pointer;
}
</style>

<script>
/* Pagination */
const rowsPerPage = 7;
let currentPage = 1;
const table = document.querySelector('table tbody');
const rows = table ? table.querySelectorAll('tr') : [];
const totalPages = Math.ceil(rows.length / rowsPerPage);
const paginationControls = document.getElementById('paginationControls');

function displayRows() {
    const start = (currentPage - 1) * rowsPerPage;
    const end = start + rowsPerPage;
    rows.forEach((row, index) => {
        row.style.display = (index >= start && index < end) ? '' : 'none';
    });
    renderPagination();
}

function renderPagination() {
    paginationControls.innerHTML = '';
    if (totalPages <= 1) return;

    const prevLi = document.createElement('li');
    prevLi.className = 'page-item' + (currentPage === 1 ? ' disabled' : '');
    const prevLink = document.createElement('a');
    prevLink.className = 'page-link';
    prevLink.href = '#';
    prevLink.innerHTML = '&laquo; Prev';
    prevLink.onclick = (e) => {
        e.preventDefault();
        if (currentPage > 1) {
            currentPage--;
            displayRows();
        }
    };
    prevLi.appendChild(prevLink);
    paginationControls.appendChild(prevLi);

    for (let i = 1; i <= totalPages; i++) {
        const li = document.createElement('li');
        li.className = 'page-item' + (i === currentPage ? ' active' : '');
        const link = document.createElement('a');
        link.className = 'page-link';
        link.href = '#';
        link.textContent = i;
        link.onclick = (e) => {
            e.preventDefault();
            currentPage = i;
            displayRows();
        };
        li.appendChild(link);
        paginationControls.appendChild(li);
    }

    const nextLi = document.createElement('li');
    nextLi.className = 'page-item' + (currentPage === totalPages ? ' disabled' : '');
    const nextLink = document.createElement('a');
    nextLink.className = 'page-link';
    nextLink.href = '#';
    nextLink.innerHTML = 'Next &raquo;';
    nextLink.onclick = (e) => {
        e.preventDefault();
        if (currentPage < totalPages) {
            currentPage++;
            displayRows();
        }
    };
    nextLi.appendChild(nextLink);
    paginationControls.appendChild(nextLi);
}

displayRows();

/* Modal Preview */
const modal = document.getElementById('imageModal');
const modalImg = document.getElementById('modalImage');
const thumbnails = document.querySelectorAll('.preview-img');

thumbnails.forEach(img => {
    img.addEventListener('click', function() {
        modal.style.display = 'block';
        modalImg.src = this.src;
    });
});

function closeModal() {
    modal.style.display = 'none';
}
</script>

<?php include_once('layouts/footer.php'); ?>
