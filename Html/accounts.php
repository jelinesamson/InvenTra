    <?php
        session_start();
        include '../Api/config.php';

        if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
            header("Location: systemNav.php");
            exit;
        }

        $pendingResult = $conn->query("SELECT * FROM accounts WHERE status = 'pending'");
        if (!$pendingResult) {
            die("Query failed: " . $conn->error);
        }

        $approvedResult = $conn->query("SELECT * FROM accounts WHERE status = 'approved'");
        if (!$approvedResult) {
            die("Query failed: " . $conn->error);
        }
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>EduTrack</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="../Css/systemNav.css" />
        <link rel="stylesheet" href="../Css/accounts.css">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet" />
    </head>
   <body >
        <div class="systemNav-container">
            

            <!-- Sidebar -->
            <?php include("systemNav.php"); ?>

            <!-- Main -->
            <main class="main">
                <div class="main-content">
                <header class="topbar">
                    <button class="hamburger" id="menuBtn">
                        <i data-lucide="menu"></i></button>
                </header>

            <div class="container">
                <h2 class="text-center mb-4">Accounts</h2>

                <ul class="nav nav-tabs mb-4" id="accountsTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab" aria-controls="pending" aria-selected="true">Pending</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="approved-tab" data-bs-toggle="tab" data-bs-target="#approved" type="button" role="tab" aria-controls="approved" aria-selected="false">Approved</button>
                    </li>
                </ul>

                <div class="tab-content" id="accountsTabContent">
                    <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                        <table class="table table-striped table-bordered align-middle w-100">
                            <thead class="table-primary">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($pendingResult->num_rows > 0): ?>
                                    <?php while ($row = $pendingResult->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $row['account_id'] ?></td>
                                            <td><?= htmlspecialchars($row['firstName'] . ' ' . $row['lastName']) ?></td>
                                            <td><?= htmlspecialchars($row['email']) ?></td>
                                            <td>
                                                <form method="POST" action="../Api/approveUser.php">
                                                    <input type="hidden" name="user_id" value="<?= $row['account_id'] ?>">
                                                    <select class="form-select form-select-sm roleSelect" name="role">
                                                        <option value="">Select Role</option>
                                                        <option value="admin" <?= ($row['role'] === 'admin') ? 'selected' : '' ?>>Admin</option>
                                                        <option value="cashier" <?= ($row['role'] === 'cashier') ? 'selected' : '' ?>>Cashier</option>
                                                    </select>
                                            </td>
                                            <td>
                                                    <button class="btn btn-success btn-sm mb-1 approveBtn" type="button" name="action" value="approve" data-userid="<?= $row['account_id'] ?>">Approve</button>
                                                    <button class="btn btn-danger btn-sm mb-1 deleteBtn" type="button" name="action" value="delete" data-userid="<?= $row['account_id'] ?>">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <strong>No pending accounts</strong>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="tab-pane fade" id="approved" role="tabpanel" aria-labelledby="approved-tab">
                        <table class="table table-striped table-bordered align-middle w-100">
                            <thead class="table-secondary">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($approvedResult->num_rows > 0): ?>
                                    <?php while ($row = $approvedResult->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $row['account_id'] ?></td>
                                            <td><?= htmlspecialchars($row['firstName'] . ' ' . $row['lastName']) ?></td>
                                            <td><?= htmlspecialchars($row['email']) ?></td>
                                            <td><?= htmlspecialchars($row['role']) ?></td>
                                            <td>
                                                <button class="btn btn-danger btn-sm deleteBtn" type="button" data-userid="<?= $row['account_id'] ?>">Delete</button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <strong>No approved accounts</strong>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            </main>

        </div>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://unpkg.com/lucide@latest"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (window.location.hash === '#approved') {
                    var approvedTabEl = document.querySelector('#approved-tab');
                    if (approvedTabEl) {
                        var approvedTab = new bootstrap.Tab(approvedTabEl);
                        approvedTab.show();
                    }
                }
            });
        </script>
        <script src="../Script/systemNav.js"></script>
        <script src="../Script/accounts.js"></script>
        </body>
 </html>