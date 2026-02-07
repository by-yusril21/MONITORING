<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>



    <li class="nav-item d-none d-sm-inline-block">
      <a href="https://wa.me/082296181109" class="nav-link">kontak</a>
    </li>
  </ul>

  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto">

    <?php if (!isset($_GET['page']) || $_GET['page'] == "dashboard" || $_GET['page'] == "chart") { ?>
      <li class="nav-item d-none d-sm-inline-block">
        <a class="nav-link" id="status" style="color:red;">Offline</a>
      </li>
    <?php } ?>

    <li class="nav-item">
      <a class="nav-link" data-widget="fullscreen" href="#" role="button">
        <i class="fas fa-expand-arrows-alt"></i>
      </a>
    </li>

    <?php if (!isset($_GET['page']) || $_GET['page'] == "dashboard") { ?>
      <li class="nav-item">
        <a class="nav-link" id="lock">
          <i class="fas fa-key" id="lockIcon"></i>
        </a>
      </li>
    <?php } ?>

  </ul>
</nav>