  <ul class="navbar-nav sidebar sidebar-light accordion " style="position: sticky;" id="accordionSidebar">
    <a class="sidebar-brand d-flex align-items-center bg-gradient-primary  justify-content-center" href="index.php">
      <div class="sidebar-brand-icon">
        <img src="img/logo/logo.jpg">
      </div>
      <div class="sidebar-brand-text mx-3"></div>
    </a>
    <hr class="sidebar-divider my-0">
    <li class="nav-item active">
      <a class="nav-link" href="index.php">
        <i class="fas fa-fw fa-tachometer-alt"></i>
        <span>Dashboard</span></a>
      <hr class="sidebar-divider">
    </li>
    <div class="sidebar-heading">
      Students
    </div>
    </li>
    <li class="nav-item">
      <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseBootstrap2" aria-expanded="true" aria-controls="collapseBootstrap2">
        <i class="fas fa-user-graduate"></i>
        <span>Manage Students</span>
      </a>
      <div id="collapseBootstrap2" class="collapse" aria-labelledby="headingBootstrap" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
          <h6 class="collapse-header">Manage Students</h6>
          <a class="collapse-item" href="studentunre.php">Unregistered Students</a>
          <a class="collapse-item" href="createStudents.php">Registered Students</a>

          <!-- <a class="collapse-item" href="#">Assets Type</a> -->
        </div>
      </div>
    </li>
    <hr class="sidebar-divider">
    <div class="sidebar-heading">
      RFID Management
    </div>
    <li class="nav-item">
      <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePage" aria-expanded="true"
        aria-controls="collapsePage">
        <i class="fas fa-fw fa-columns"></i>
        <span>Management</span>
      </a>
      <div id="collapsePage" class="collapse" aria-labelledby="headingPage" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
          <h6 class="collapse-header">Manage</h6>
          <a class="collapse-item" href="rfidlist.php">Rfid List</a>
          <a class="collapse-item" href="blockrfid.php">Blocked Student</a>
          

        </div>
      </div>
    </li>
    <hr class="sidebar-divider">
    <div class="sidebar-heading">
      Reports
    </div>
    <li class="nav-item">
      <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseBootstrapschemes"
        aria-expanded="true" aria-controls="collapseBootstrapschemes">
        <i class="fas fa-home"></i>
        <span>Daily Logs</span>
      </a>
      <div id="collapseBootstrapschemes" class="collapse" aria-labelledby="headingBootstrap" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
          <h6 class="collapse-header">Daily Logs</h6>
          <a class="collapse-item" href="studentdailylogs.php">Student Daily Log</a>

        </div>
      </div>
    </li>

    <li class="nav-item">
      <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTable" aria-expanded="true"
        aria-controls="collapseTable">
        <i class="fas fa-fw fa-table"></i>
        <span>Reports</span>
      </a>
      <div id="collapseTable" class="collapse" aria-labelledby="headingTable" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
          <h6 class="collapse-header">Reports </h6>
          <a class="collapse-item" href="studreports.php">Student Reports</a>
      </div>
    </li>
    <hr class="sidebar-divider">

  </ul>