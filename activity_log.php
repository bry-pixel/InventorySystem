<?php
$page_title = 'Activity Logs';
require_once('includes/load.php');
page_require_level(1);

// Get all users for dropdown
$users = find_by_sql("SELECT id, name FROM users ORDER BY name ASC");

// Filter: user
$user_filter = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

// Sort setup
$valid_columns = ['action','description','page','created_at'];
$sort = isset($_GET['sort']) && in_array($_GET['sort'],$valid_columns) ? $_GET['sort'] : 'created_at';
$order = (isset($_GET['order']) && strtolower($_GET['order']) === 'asc') ? 'ASC' : 'DESC';

// Pagination
$limit = 20;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Sorting icons helper
function sort_icon($column,$sort,$order){
    if($column == $sort){
        return $order === 'ASC' ? " ↑" : " ↓";
    }
    return "";
}
function next_order($column,$sort,$order){
    if($column == $sort){
        return ($order === 'ASC') ? 'desc' : 'asc';
    }
    return 'asc';
}

// Build query
$where = $user_filter > 0 ? "WHERE a.user_id = {$user_filter}" : "";

$total_sql = "SELECT COUNT(*) AS total FROM activity_logs a {$where}";
$total_res = $db->query($total_sql);
$total_logs = $db->fetch_assoc($total_res)['total'];
$total_pages = ceil($total_logs / $limit);

$sql = "
  SELECT a.*, u.name AS username
  FROM activity_logs a
  LEFT JOIN users u ON a.user_id = u.id
  {$where}
  ORDER BY a.{$sort} {$order}
  LIMIT {$limit} OFFSET {$offset}
";
$logs = find_by_sql($sql);
?>

<?php include_once('layouts/header.php'); ?>


<div class="row">
  <div class="col-md-12">
    <?= display_msg($msg); ?>
  </div>

  <div class="col-md-12">
    <div class="panel panel-default shadow-sm">

      <!-- Panel Header -->
      <div class="panel-heading d-flex justify-content-between align-items-center"
           style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap;">
        <h3 class="panel-title" style="margin:0;">
          <span class="glyphicon glyphicon-list-alt"></span> Activity Logs
        </h3>

        <!-- User Filter Dropdown (auto-submit like category filter) -->
        <div class="text-center" style="margin:0;">
        <form method="GET" class="form-inline d-inline-block" style="display:inline-block;">
            <select name="user_id" class="form-control input-sm" style="margin-right:5px; font-size:14px;" onchange="this.form.submit()">
              <option value="0">All Users</option>
              <?php foreach($users as $u): ?>
                <option value="<?= $u['id']; ?>" <?= ($user_filter == $u['id']) ? 'selected' : ''; ?>>
                  <?= remove_junk($u['name']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </form>
        </div>
      </div>

      <!-- Table -->
      <div class="panel-body">
        <?php if (!empty($logs)): ?>
        <table class="table table-hover table-bordered table-striped">
          <thead style="background:#f8f9fa;">
            <tr>
              <th style="width:50px;">#</th>
              <th>User</th>
              <th>
                <a href="?sort=action&order=<?= next_order('action',$sort,$order) ?>&user_id=<?= $user_filter ?>">
                  Action<?= sort_icon('action',$sort,$order) ?>
                </a>
              </th>
              <th>Description</th>
              <th>
                <a href="?sort=page&order=<?= next_order('page',$sort,$order) ?>&user_id=<?= $user_filter ?>">
                  Page<?= sort_icon('page',$sort,$order) ?>
                </a>
              </th>
              <th>
                <a href="?sort=created_at&order=<?= next_order('created_at',$sort,$order) ?>&user_id=<?= $user_filter ?>">
                  Date<?= sort_icon('created_at',$sort,$order) ?>
                </a>
              </th>
            </tr>
          </thead>
          <tbody>
            <?php $count = $offset + 1; ?>
            <?php foreach ($logs as $log): ?>
              <tr style="font-size:14px;">
                <td class="text-center"><?= $count++; ?></td>
                <td><strong><?= remove_junk(ucwords($log['username'])); ?></strong></td>
                <td><?= remove_junk($log['action']); ?></td>
                <td><?= remove_junk($log['description']); ?></td>
                <td class="text-center"><?= remove_junk($log['page']); ?></td>
                <td class="text-center"><?= date("M d, Y h:i A", strtotime($log['created_at'])); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <!-- Pagination -->
        <div class="text-center mt-3">
          <ul class="pagination pagination-sm" style="margin:0;">
            <li class="<?= ($page <= 1) ? 'disabled' : ''; ?>">
              <a href="?user_id=<?= $user_filter; ?>&page=<?= max(1, $page-1); ?>&sort=<?= $sort; ?>&order=<?= $order; ?>">« Prev</a>
            </li>

            <?php for ($i=1; $i<=$total_pages; $i++): ?>
              <li class="<?= ($page == $i) ? 'active' : ''; ?>">
                <a href="?user_id=<?= $user_filter; ?>&page=<?= $i; ?>&sort=<?= $sort; ?>&order=<?= $order; ?>"><?= $i; ?></a>
              </li>
            <?php endfor; ?>

            <li class="<?= ($page >= $total_pages) ? 'disabled' : ''; ?>">
              <a href="?user_id=<?= $user_filter; ?>&page=<?= min($total_pages, $page+1); ?>&sort=<?= $sort; ?>&order=<?= $order; ?>">Next »</a>
            </li>
          </ul>
        </div>

        <?php else: ?>
          <div class="alert alert-info text-center">No activity logs found.</div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>


<?php include_once('layouts/footer.php'); ?>
