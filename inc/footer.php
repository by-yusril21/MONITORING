<footer class="main-footer text-white" style="background-color:#212529; padding: 10px;">
  <div class="footer-container">
    <!-- Default to the left -->
    <div class="footer-content">
      <strong>Copyright &copy; OscarAudio2024
        <a href="https://oscarAudio.com" class="text-decoration-none text-white">website</a>.
      </strong>Project Tugas Akhir.
    </div>

    <div class="social-icons">
      <a href="https://instagram.com/yusril_oscar" target="_blank" class="text-white social-icon">
        <i class="fab fa-instagram"></i>
      </a>
      <a href="https://www.youtube.com/@yusrilaudiochannel4204" target="_blank" class="text-white social-icon">
        <i class="fab fa-youtube"></i>
      </a>
      <a href="https://www.facebook.com/profile.php?id=100022192622408" target="_blank" class="text-white social-icon">
        <i class="fab fa-facebook-f"></i>
      </a>
      <a href="https://www.tiktok.com/@yusriloscar?is_from_webapp=1&sender_device=pc" target="_blank"
        class="text-white social-icon">
        <i class="fab fa-tiktok"></i>
      </a>
    </div>

    <!-- To the right -->
    <div class="version">
      versi 1.1
    </div>
  </div>
</footer>

<!-- Include Font Awesome -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<style>
  .main-footer {
    background-color: #212529;
    padding: 20px;
  }

  .footer-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
  }

  .footer-content {
    display: inline-block;
    margin-bottom: 5px;
  }

  .social-icons {
    text-align: center;
    margin-bottom: 5px;
  }

  .social-icon {
    margin-right: 30px;
    margin-left: 30px;
    font-size: 18px;
    /* Ukuran ikon media sosial */
    transition: transform 0.3s, color 0.3s;
    /* Animasi perubahan ukuran dan warna */
  }

  .social-icon:hover {
    transform: scale(1.3);
    /* Membesarkan ikon saat hover */
    color: inherit;
    /* Menggunakan warna asli ikon */
  }

  .version {
    display: inline-block;
    margin-top: 1px;
    /* Tambahkan jarak atas jika diperlukan */
    margin-bottom: 10px;
    /* Tambahkan jarak atas jika diperlukan */
    margin-right: 10px;
  }

  @media (max-width: 767px) {
    .footer-container {
      flex-direction: column;
      align-items: center;
    }

    .footer-content {
      text-align: center;
      margin-bottom: 5px;
    }

    .social-icons {
      margin-bottom: 10px;
      margin-top: 5px;
    }

    .social-icon {
      margin-right: 20px;
      margin-left: 20px;
    }

    .version {
      text-align: center;
      display: block;
      margin-bottom: 0;
    }
  }
</style>