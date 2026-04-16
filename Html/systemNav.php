
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
$firstName = isset($_SESSION['user']) ? $_SESSION['user'] : '';
?>

        <div class="custom-sidebar">
            <nav class="sidebar" id="sidebar">
            <div class="profile">
                <h3><?= htmlspecialchars(ucfirst($firstName)) ?></h3>
                <p><?= htmlspecialchars(ucfirst($role)) ?></p>
            </div>

            <ul class="nav-links">
                <?php if ($role === 'admin'): ?>
                <li class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
                    <a href="../Html/dashboard.php">
                        <i data-lucide="layout-dashboard"></i>
                        Dashboard
                    </a>
                </li>

                <li class="<?= basename($_SERVER['PHP_SELF']) == 'productManagement.php' ? 'active' : '' ?>">
                    <a href="../Html/productManagement.php">
                        <i data-lucide="package"></i>
                        Product Management
                    </a>
                </li>

                <li class="<?= basename($_SERVER['PHP_SELF']) == 'incomingProducts.php' ? 'active' : '' ?>">
                    <a href="../Html/incomingProducts.php">
                        <i data-lucide="truck"></i>
                        Incoming Products
                    </a>
                </li>

                <li class="<?= basename($_SERVER['PHP_SELF']) == 'inventoryManagement.php' ? 'active' : '' ?>">
                    <a href="../Html/inventoryManagement.php">
                        <i data-lucide="warehouse"></i>
                        Inventory History
                    </a>
                </li>
                    

                <?php endif; ?>

                <?php if (in_array($role, ['admin', 'cashier'])): ?>
                <li class="<?= basename($_SERVER['PHP_SELF']) == 'salesManagement.php' ? 'active' : '' ?>">
                    <a href="../Html/salesManagement.php">
                        <i data-lucide="shopping-cart"></i>
                        Sales Management
                    </a>
                </li>
                <?php endif; ?>

                <?php if ($role === 'admin'): ?>
                <li class="<?= basename($_SERVER['PHP_SELF']) == 'salesReport.php' ? 'active' : '' ?>">
                    <a href="../Html/salesReport.php">
                        <i data-lucide="bar-chart-3"></i>
                        Sales Report
                    </a>
                </li>
                
                <li class="<?= basename($_SERVER['PHP_SELF']) == 'accounts.php' ? 'active' : '' ?>">
                    <a href="../Html/accounts.php">
                        <i data-lucide="users"></i>
                        Accounts
                    </a>
                </li>
                <?php endif; ?>

            </ul>

            <div class="logout">
                <a href="../Api/logout.php" class="btn-danger-custom logoutBtn">
                    <i data-lucide="log-out"></i>
                    Logout
                </a>
            </div>
            </nav>
        </div>    