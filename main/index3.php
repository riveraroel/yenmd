<?php
    include('connection/conn.php');
    include('check_session.php');
?>
<html>
<head>
    <title>Mary Yentl Borazon, MD - Virtual Clinic</title>
    <meta charset="utf-8">
    <link rel="prefetch" href="https://cdn.svgator.com/images/2023/06/hearthbeat-svg-loader-animation.svg" as="image" type="image/svg+xml"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/x-icon" href="../favicon/favicon.png">
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css'>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Full Calendar -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ" crossorigin="anonymous"></script>  -->
    <link href='https://unpkg.com/fullcalendar@3.10.0/dist/fullcalendar.min.css' rel='stylesheet' />
    <link href='https://unpkg.com/fullcalendar@3.10.0/dist/fullcalendar.print.css' rel='stylesheet' media='print' />
    <script src='https://unpkg.com/moment@2.24.0/min/moment.min.js'></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
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
<body onload="myFunction()" style="margin:0;">
    <div id="preloader">
        <div class = 'preloader-logo'>
            <img src="hearthbeat-svg-loader-animation.svg" alt="">
        </div>
    </div>
    <div class="container w-100 p-4 mt-1 mb-4 bg-body-tertiary rounded-6 animate-bottom" id="main-content" style="display: none; max-width: 900px;">
<!-- Top Navbar for Tabs -->
<nav class="navbar navbar-expand-lg navbar-light bg-light rounded-3 px-0 sticky-top shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">
    <img src="../favicon/banner.png" alt="Yen MD logo" style="height: 50px;" class="img-fluid">
</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTabs" aria-controls="navbarTabs" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

<div class="collapse navbar-collapse justify-content-end" id="navbarTabs">
    <ul class="navbar-nav nav nav-pills ms-auto text-end flex-lg-row flex-column align-items-lg-center align-items-end" id="pills-tab" role="tablist">

                <li class="nav-item" role="presentation">
                    <button class="nav-link active text-primary fw-semibold" id="pills-apt_calendar-tab" data-bs-toggle="pill" data-bs-target="#pills-apt_calendar" type="button" role="tab" aria-controls="pills-apt_calendar" aria-selected="true">Appointment Calendar</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link text-primary fw-semibold" id="pills-px_profile-tab" data-bs-toggle="pill" data-bs-target="#pills-px_profile" type="button" role="tab" aria-controls="pills-px_profile" aria-selected="false">Patients</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link text-primary fw-semibold" id="pills-createacc-tab" data-bs-toggle="pill" data-bs-target="#pills-createacc" type="button" role="tab" aria-controls="pills-createacc" aria-selected="false">User Account</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link text-primary fw-semibold" id="pills-logout-tab" type="button" role="tab">Logout</button>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Tab Content -->
<div class="tab-content border rounded-3 border-primary p-3 mt-3" id="pills-tabContent">
    <?php
        include 'php/calendar/index.php';
        include 'php/pxprofile/index.php';
        include 'php/reporting/index.php';
        include 'php/createaccount/index.php';
    ?>
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
                        <h5>Submitting patient......</h5>
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
                        <h5>Submitting account......</h5>
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
    const navbarCollapse = document.querySelector('.navbar-collapse');

    // Auto-close menu when a nav link is clicked (on mobile)
    navLinks.forEach(function (link) {
      link.addEventListener('click', function () {
        const collapse = bootstrap.Collapse.getInstance(navbarCollapse);
        if (collapse && window.innerWidth < 992) {
          collapse.hide();
        }
      });
    });

    // Switch default tab to "Patients" on mobile
    if (window.innerWidth < 992) {
      const defaultTab = document.getElementById("pills-px_profile-tab");
      if (defaultTab) {
        defaultTab.click(); // simulate click to activate Patients tab
      }
    }
  });
</script>
</body>
</html>