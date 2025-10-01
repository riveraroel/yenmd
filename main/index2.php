<?php
    include('connection/conn.php');
    include('check_session.php');
?>
<html>
<head>
    <title>Virtual Clinic</title>
    <meta charset="utf-8">
    <link rel="prefetch" href="https://cdn.svgator.com/images/2023/06/hearthbeat-svg-loader-animation.svg" as="image" type="image/svg+xml"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <link rel="icon" type="image/x-icon" href="../favicon/myicon.ico">
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css'>
    <link rel="stylesheet" href="css/style.css">
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js'></script>
    <!-- Full Calendar -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ" crossorigin="anonymous"></script> 
    <link href='https://unpkg.com/fullcalendar@3.10.0/dist/fullcalendar.min.css' rel='stylesheet' />
    <link href='https://unpkg.com/fullcalendar@3.10.0/dist/fullcalendar.print.css' rel='stylesheet' media='print' />
    <script src='https://unpkg.com/moment@2.24.0/min/moment.min.js'></script>    
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script src='https://unpkg.com/fullcalendar@3.10.0/dist/fullcalendar.min.js'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script>
    <script src="https://unpkg.com/tooltip.js@1.3.2/dist/umd/tooltip.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!---Datepicker-->  
    <script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
    <link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <!---DataTables-->  
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js" type="text/javascript"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.js" type="text/javascript"></script>
    <link href='https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.css' rel='stylesheet' />
    <script>
        function isNumber(evt) 
        {
            evt = (evt) ? evt : window.event;
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57)) 
            {
            return false;
            }
            return true;
        }
        var myVar;
        function myFunction() 
        {
            myVar = setTimeout(showPage, 2500);
        }
        function showPage() 
        {
            document.getElementById("preloader").style.display = "none";
            document.getElementById("main-content").style.display = "block";
            $('#calendar').fullCalendar('render');
        }
        $(window).on('load', function() {
      // Adjust any tooltips if needed
      $('[data-bs-toggle="tooltip"]').tooltip('update');
  });
    </script>
        <script type="text/javascript" src = 'js/get.js<?php echo "?version=".$version?>'></script>
        <script type="text/javascript" src = 'js/script.js<?php echo "?version=".$version?>'></script>
</head>
<body style="margin:0;">
    <div id="preloader">
        <div class = 'preloader-logo'>
            <img src="hearthbeat-svg-loader-animation.svg" alt="Loading...">
        </div>
    </div>
    <br>
    <div class='container-fluid p-3 p-md-4 mb-4 bg-body-tertiary rounded-3 animate-bottom' id='main-content'>
        <div class = 'row'>
            <div class = 'col-md-12 text-center'>
                <img class = 'thumbnail img-fluid' src="../favicon/banner.png" alt="Banner" width = '550'>
            </div>
        </div>
<div class="container-fluid px-3 px-md-4 d-flex flex-column flex-md-row align-items-start">
  <!-- 🔘 Sidebar Toggle Button for Mobile -->
  <button class="btn btn-outline-primary d-md-none mb-3" id="toggleSidebar">
    <i class="material-icons">menu</i> Menu
  </button>

  <!-- 📚 Sidebar (hidden on mobile, visible on md and up) -->
  <div class="sidebar-container d-md-block me-md-3" id="sidebarContainer">
    <ul class="nav nav-pills flex-column border-end border-3 align-items-end" id="pills-tab" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link text-primary fw-semibold active position-relative"
          id="pills-apt_calendar-tab" data-bs-toggle="pill" data-bs-target="#pills-apt_calendar"
          type="button" role="tab" aria-controls="pills-apt_calendar" aria-selected="true">
          Appointment Calendar
        </button>
      </li>
      <li class="nav-item ds" role="presentation">
        <button class="nav-link text-primary fw-semibold position-relative"
          id="pills-px_profile-tab" data-bs-toggle="pill" data-bs-target="#pills-px_profile"
          type="button" role="tab" aria-controls="pills-px_profile" aria-selected="false">
          Patients
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link text-primary fw-semibold position-relative"
          id="pills-createacc-tab" data-bs-toggle="pill" data-bs-target="#pills-createacc"
          type="button" role="tab" aria-controls="pills-createacc" aria-selected="false">
          User Account
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link text-primary fw-semibold position-relative"
          id="pills-logout-tab" type="button">
          Logout
        </button>
      </li>
    </ul>
  </div>

  <!-- 🧱 Main content area next to sidebar -->
  <div class="tab-content border rounded-3 border-primary p-3 w-100 h-100" id="pills-tabContent">
    <?php
      include 'php/calendar/index.php';
      include 'php/pxprofile/index.php';
      include 'php/reporting/index.php';
      include 'php/createaccount/index.php';
    ?>
  </div>
</div>           
        </div>
    </div>
    <div class="modal fade" id="notifierBanner" tabindex="-1" aria-labelledby="notifierBannerLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="notifierBannerLabel">Virtual Clinic</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id ="BannerContent">
                    Invalid data. Please try again.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id = 'bannerBtn'>Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id = 'loadingModal' class = "modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog">
        <div class = 'modal-dialog modal-dialog-centered modal-lg' role="document">
            <div class="modal-content">
                <div class = 'modal-header'>
                    <h4 class = 'modal-title'>Virtual Clinic</h4>
                </div>
                <div class = 'modal-body'>
                    <div class = 'text-center'>
                        <h5><p>Submitting patient......</p></h5>
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id = 'loadingModal_acc' class = "modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog">
        <div class = 'modal-dialog modal-dialog-centered modal-lg' role="document">
            <div class="modal-content">
                <div class = 'modal-header'>
                    <h4 class = 'modal-title'>Virtual Clinic</h4>
                </div>
                <div class = 'modal-body'>
                    <div class = 'text-center'>
                        <h5><p>Submitting account......</p></h5>
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script>
  function myFunction() {
    setTimeout(() => {
      document.getElementById("preloader").style.display = "none";
      document.getElementById("main-content").style.display = "block";
      if ($('#calendar').length) {
        $('#calendar').fullCalendar('render');
      }
    }, 2500);
  }

  window.onload = myFunction;
</script>
<script>
  // Toggle sidebar on small screens
  $(document).ready(function () {
    $('#toggleSidebar').on('click', function () {
      $('#sidebarContainer').toggleClass('show');
    });

    // Auto-hide sidebar when a nav link is clicked on small screens
    $('#sidebarContainer button.nav-link').on('click', function () {
      if (window.innerWidth < 768) {
        $('#sidebarContainer').removeClass('show');
      }
    });
  });
</script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    if (window.innerWidth < 768) {
      // Remove 'active' from the Appointment tab and content
      document.getElementById('pills-apt_calendar-tab').classList.remove('active');
      document.getElementById('pills-apt_calendar').classList.remove('active', 'show');

      // Add 'active' to the Patients tab and content
      document.getElementById('pills-px_profile-tab').classList.add('active');
      document.getElementById('pills-px_profile').classList.add('active', 'show');
    }
  });
</script>
</body>
</html>