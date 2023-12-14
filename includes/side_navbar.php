<?php 
  require_once 'db_conn.php';
?>
<header>
  <nav class="navbar header-navbar1">
      <div class="container">
          <div class="logo">
              <h3>
                  <?php
                  // Get the current page filename
                  $currentPage = basename($_SERVER['PHP_SELF']);

                  // Set the page title based on the current page
                  switch ($currentPage) {
                    case 'pos.php':
                        echo 'POINT OF SALE  <i class="bx bx-purchase-tag bx-sm"></i>';
                      break;
                      case 'inventory.php':
                          echo 'INVENTORY SECTION  <i class="bx bx-detail"></i> ';
                          break;
                      case 'admin_management.php':
                          echo 'EMPLOYEE MANAGEMENT  <i class="bx bxs-user-account"></i>';
                          break;
                      case 'sales_history.php':
                          echo 'SALES HISTORY <i class="bx bx-history"></i>';
                          break;
                      case 'sales_report.php':
                          echo 'SALES REPORT  <i class="bx bxs-report"></i>';
                          break;
                      case 'dashboard.php':
                          echo 'DASHBOARD  <i class="bx bxs-dashboard"></i>';
                          break;
                        case 'settings.php':
                          echo 'SETTINGS  <i class="bx bxs-cog"></i>';
                          break;
                      default:
                          // Set a default title if none of the cases match
                          echo 'Unknown Page';
                          break;
                  }
                  ?>
              </h3>
          </div>
                <!-- <?php 
                  $user_id = $_SESSION['user_id'];
                  $sql_user = "SELECT username, user_type FROM users WHERE user_id = '$user_id'";
                  $sqlres = query($conn, $sql_user);
                  
                  foreach($sqlres as $reso){
                    $username = $reso['username'];
                    $type = $reso['user_type'];
                  }
                ?>
                  <span style="color: white; margin: 5em;"><b><?php echo "HELLO" . $username .", ". $type ;?></b></span> -->
                  <!-- <span style="color: white; " id="realTimeClock"><?php echo date('Y-m-d H:i:s'); ?></span> -->
              <div class="nav-item ml-auto">
              <button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#logout">
              <i class='bx bx-log-out bx-sm'></i></button>
              <div class="modal fade" id="logout" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h1 class="modal-title fs-5" id="staticBackdropLabel" style="color: black;">LOGOUT</h1>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <p style="color: black;">Are you sure you want to end your session?</p>
                  </div>
                  <div class="modal-footer">
                    <form action="" method="GET">
                      <button type="button" class="btn" data-bs-dismiss="modal">Cancel</button>
                      <button type="submit" class="btn btn-secondary" name="logout">Proceed</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
        </div>
      </div>
  </nav>
  </header>
  <aside>
    <div class="sidebar">
      <div class="top">
        <div class="logo">
          <i class='bx bxs-store'></i>
          <span>JGS Infrastructure<br> Builders Inc.</span>
        </div>
        <i class='bx bx-menu' id="btn"></i>
      </div>
      <ul>
        <li>
          <a href="dashboard.php" class="nav-item1">
            <i class='bx bxs-dashboard'></i>
            <span class="nav-item">Dashboard</span>
          </a>
          <span class="tooltip1">Dashboard</span>
        </li>
        <li>
          <a href="pos.php" class="nav-item1">
            <i class='bx bx-purchase-tag'></i>
            <span class="nav-item">POS</span>
          </a>
          <span class="tooltip1 d-flex">Point of Sales</span>
        </li>
        <li>
          <a href="inventory.php" class="nav-item1">
            <i class='bx bx-detail'></i>
            <span class="nav-item">Inventory</span>
          </a>
          <span class="tooltip1">Inventory</span>
        </li>
        <li class="nav-item1 dropdown" id="salesDropdown">
          <a class="nav-item1 dropdown-toggle another-dropdown" role="button" href="<?php echo getSalesLink(); ?>" data-bs-toggle="collapse" data-bs-auto-close="outside" aria-expanded="false">
            <i class='bx bx-bar-chart-square'></i>
            <span class="nav-item dropdown">Sales</span>
          </a>
          <ul class="dropdown-menu">
            <li>
              <a href="sales_history.php" class="nav-item1" ><i class='bx bx-history'></i>Sales History</a>
            </li>
            <li>
              <a href="sales_report.php" class="nav-item1"><i class='bx bxs-report'></i>Sales Report</a>
            </li>
          </ul>
          <span class="tooltip1">Sales</span>
        </li>
        <li>
          <a href="admin_management.php" class="nav-item1">
            <i class='bx bxs-user-account'></i>
            <span class="nav-item">E.Management</span>
          </a>
          <span class="tooltip1">Employee Management</span>
        </li>
        <li>
          <a href="settings.php" class="nav-item1">
            <i class='bx bxs-cog'></i>
            <span class="nav-item">Settings</span>
          </a>
          <span class="tooltip1">Settings</span>
        </li>
      </ul>
    </div>
  </aside>
  <script>
    const currentURL = window.location.href;
    const navLinks = document.querySelectorAll('.nav-item1');

    navLinks.forEach(link => {
      const linkURL = link.getAttribute('href');
      
      if (currentURL.includes(linkURL)) {
        link.classList.add('active'); 
      }
    });

    // Toggle sidebar functionality (unchanged)
    let btn = document.querySelector('#btn');
    let sidebar = document.querySelector('.sidebar');

    btn.onclick = function () {
      sidebar.classList.toggle('active');
    };
  </script>

<script>
    // Enable Bootstrap dropdowns
    var dropdowns = document.querySelectorAll('.dropdown-toggle');
    dropdowns.forEach(function(dropdown) {
        dropdown.addEventListener('click', function(event) {
            event.preventDefault();
            event.stopPropagation();
            var parent = dropdown.parentElement;
            var menu = parent.querySelector('.dropdown-menu');
            var isOpen = menu.classList.contains('show');

            // Close all other open dropdowns
            dropdowns.forEach(function(otherDropdown) {
                var otherParent = otherDropdown.parentElement;
                var otherMenu = otherParent.querySelector('.dropdown-menu');
                if (otherParent !== parent && otherMenu.classList.contains('show')) {
                    otherMenu.classList.remove('show');
                    otherParent.classList.remove('show');
                }
            });

            // Toggle the 'show' class
            if (isOpen) {
                menu.classList.remove('show');
                parent.classList.remove('show');
            } else {
                menu.classList.add('show');
                parent.classList.add('show');
            }
        });
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        dropdowns.forEach(function(dropdown) {
            var parent = dropdown.parentElement;
            var menu = parent.querySelector('.dropdown-menu');
            if (parent.classList.contains('show') && !menu.contains(event.target) && !dropdown.contains(event.target)) {
                menu.classList.remove('show');
                parent.classList.remove('show');
            }
        });
    });
</script>
<?php
function getSalesLink() {
    $current_page = basename($_SERVER['PHP_SELF']);
    
    if ($current_page == 'sales_history.php' || $current_page == 'sales_report.php') {
        return $current_page;
    } else {
        return 'inactive.php';
    }
}
?>