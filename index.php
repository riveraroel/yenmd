<html>
<head>
  <meta charset="utf-8">
  <title>Mary Yentl Borazon, MD - Virtual Clinic</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="icon" type="image/x-icon" href="favicon/favicon.png">
  <!-- SEO Meta Tags -->
  <meta name="description" content="Consult with Dr. Mary Yentl Borazon, MD through her secure and user-friendly Virtual Clinic. Book appointments, access prescriptions, and manage your health online.">
  <meta name="keywords" content="Mary Yentl Borazon, MD, Virtual Clinic, Online Consultation, Medical Appointments, Telemedicine, Doctor in the Philippines">
  <meta name="author" content="Mary Yentl Borazon, MD">
  <link rel="canonical" href="https://yenmd.com">

  <!-- Bootstrap 4 -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" crossorigin="anonymous">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="css/login_style.css">

  <!-- jQuery (must come before script.js) -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

  <!-- Your script -->
  <script src="js/script.js" defer></script>
</head>
<body>

<!-- Login Form -->
<section class="container-fluid">
  <section class="row justify-content-center">
    <section class="col-12 col-sm-6 col-md-4">
      <form class="form-container" id="login" action="#">
        <div class="form-group">
          <div class="row">
            <div class="col-md-12 text-center">
              <img class="thumbnail img-fluid" src="favicon/banner.png" alt="Banner" width="490">
            </div>
          </div>
          <br>
          <label for="username">Username</label>
          <input type="text" class="form-control" id="username" name="username" placeholder="Enter username">
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" class="form-control" id="password" name="password" placeholder="Password">
        </div>
        <button type="button" class="btn btn-primary btn-block" id="submit">Sign in</button>
        <div class="form-footer"></div>
        <br>
        <!-- <p class='h6'><small><a href="javascript:void(0)" id="atag">Forgot Password?</a></small></p> -->
      </form>
    </section>
  </section>
</section>

<!-- Bootstrap + Popper JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" crossorigin="anonymous"></script>

<script>
  $(document).ready(function () {
    if (window.opener && window.opener !== window) {
      window.close();
    }
  });
</script>
</body>
</html>
