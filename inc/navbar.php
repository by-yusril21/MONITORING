<style>
  /* Animasi Teks */
  @keyframes slideFadeIn {
    0% {
      opacity: 0;
      transform: translateY(-10px);
    }

    100% {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .dynamic-title {
    font-family: 'Source Sans Pro', sans-serif;
    font-weight: 700;
    font-size: 1.25rem;
    color: #d0d0d0;
    /* Warna Emas */
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    text-transform: uppercase;
    letter-spacing: 1px;
    animation: slideFadeIn 0.5s ease-out;
    white-space: nowrap;
    /* Mencegah teks turun ke bawah */
  }

  .navbar-custom {
    background-color: #343a40 !important;
  }
</style>

<nav class="main-header navbar navbar-expand navbar-dark navbar-custom border-bottom-0">

  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>

    <li class="nav-item d-flex align-items-center ml-3">
      <span id="header-title" class="dynamic-title">
        DASHBOARD MONITORING
      </span>
    </li>
  </ul>

  <ul class="navbar-nav ml-auto">
    <li class="nav-item">
      <a class="nav-link" data-widget="fullscreen" href="#" role="button">
        <i class="fas fa-expand-arrows-alt"></i>
      </a>
    </li>
  </ul>

</nav>