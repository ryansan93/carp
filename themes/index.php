<!DOCTYPE html>
<html lang="en">

<head>
  <base href="<?php echo base_url() ?>" />
  <!-- <link rel="shortcut icon" type="image/x-icon" href="assets/images/logo.png"> -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="">
  <meta name="author" content="Dashboard">
  <meta name="keyword" content="Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">

  <title><?php echo $this->config->item('judul_aplikasi'); ?></title>

  <?php // CSS files ?>
  <?php if (isset($css_files) && is_array($css_files)) : ?>
      <?php foreach ($css_files as $css) : ?>
          <?php if ( ! is_null($css)) : ?>
              <link rel="stylesheet" href="<?php echo $css; ?>?v=<?php echo $this->settings->site_version; ?>"><?php echo "\n"; ?>
          <?php endif; ?>
      <?php endforeach; ?>
  <?php endif; ?>

</head>

<body>

  <div class="d-flex" id="wrapper">

    <!-- Sidebar -->
    <div class="bg-light-black" id="sidebar-wrapper" style="width: 17rem;">
      <div class="sidebar-heading">
        <!-- <img src="https://d3ki9tyy5l5ruj.cloudfront.net/obj/3ac85a538c3fc5bb08d0206ede04ae8aa13c20b2/inapp__logo_color_ondark_horizontal.svg" width="80%" height="80%"> -->
        <!-- <img src="assets/images/logo-mgb.jpg" width="20%" height="20%"> -->
        <b><?php echo $this->config->item('nama_aplikasi'); ?></b>
      </div>
      <div class="divider-heading" style="padding: 0rem 1rem;">
        <div class="dropdown-divider" style="margin-top: 0rem;"></div>
      </div>
      <div class="list-group list-group-flush content mCustomScrollbar" style="max-width: 20rem; width: 17rem;">
        <ul class="list-unstyled components">
          <li class="active">
            <a class="list-group-item list-group-item-action bg-light-black menu" data-txt="Dashboard" href="#">
              <i class="fa fa-dashboard"></i>
              <span>Dashboard</span>
            </a>
          </li>
          <li>
            <a href="#master" data-toggle="collapse" aria-expanded="false" data-val="0" class="dropdown-toggle list-group-item list-group-item-action bg-light-black">
              Master
            </a>
            <ul class="collapse list-unstyled" id="master">
              <li class="menu">
                <a href="master/User" class="list-group-item list-group-item-action bg-light-black menu" data-txt="User">User</a>
              </li>
              <li class="menu">
                <a href="master/Divisi" class="list-group-item list-group-item-action bg-light-black menu" data-txt="Divisi">Divisi</a>
              </li>
              <li class="menu">
                <a href="master/Branch" class="list-group-item list-group-item-action bg-light-black menu" data-txt="Branch">Branch</a>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
    <!-- /#sidebar-wrapper -->

    <!-- Page Content -->
    <div id="page-content-wrapper">

      <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom no-padding">
        <!-- <button class="btn btn-primary" id="menu-toggle">Toggle Menu</button> -->
        <a id="menu-toggle" title="Hide Menu">
          <i class="fa fa-angle-left cursor-p left"></i> 
          <i class="fa fa-navicon cursor-p"></i> 
          <i class="fa fa-angle-right cursor-p right" hidden="true"></i>
        </a>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">

          <div class="col-md-8 title"><?php echo $title_menu; ?></div>

          <ul class="navbar-nav ml-auto mt-2 mt-lg-0 pull-right">
          </ul>
        </div>
      </nav>

      <div class="container-fluid">

        <div class="main">
          <?php echo $view; ?>
        </div>
      </div>
    </div>
    <!-- /#page-content-wrapper -->

  </div>
  <!-- /#wrapper -->

  <!-- Logout Modal-->
  <div class="modal" id="logoutModal">
    <div class="modal-dialog">
      <div class="modal-content">

        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Alert</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <!-- Modal body -->
        <div class="modal-body">
          <span class="modal-title">Apakah anda yakin ingin keluar ?</span>
        </div>

        <!-- Modal footer -->
        <div class="modal-footer">
          <a class="btn btn-primary" href="user/Login/logout">Ya</a>
          <button data-dismiss="modal" class="btn btn-danger" type="button">Tidak</button>
        </div>

      </div>
    </div>
  </div>

  <?php // Javascript files ?>
  <?php if (isset($js_files) && is_array($js_files)) : ?>
      <?php foreach ($js_files as $js) : ?>
          <?php if ( ! is_null($js)) : ?>
              <?php echo "\n"; ?><script type="text/javascript" src="<?php echo $js; ?>?v=<?php echo $this->settings->site_version; ?>"></script><?php echo "\n"; ?>
          <?php endif; ?>
      <?php endforeach; ?>
  <?php endif; ?>

  <!-- Menu Toggle Script -->
  <script>
    $("#menu-toggle").click(function(e) {
      e.preventDefault();
      $("#wrapper").toggleClass("toggled");
      var togled = $("#wrapper").attr('class').split(" ");

      if ( togled.length > 1 ) {
        $("#wrapper").find('a#menu-toggle').attr('title', 'Show Menu');
        $("#wrapper").find('i.left').attr('hidden', true);
        $("#wrapper").find('i.right').removeAttr('hidden');
        $(".tu-float-btn-left").removeClass('toggled');
      } else {
        $("#wrapper").find('a#menu-toggle').attr('title', 'Hide Menu');
        $("#wrapper").find('i.left').removeAttr('hidden');
        $("#wrapper").find('i.right').attr('hidden', true);
        $(".tu-float-btn-left").addClass('toggled');
      };
    });

    $(".dropdown-toggle").click(function(e) {
      $(this).closest('li').toggleClass("open");
    });

    (function($){
      $(window).on("load",function(){
        
        $("#content-1").mCustomScrollbar({
          theme:"minimal"
        });
        
      });
    })(jQuery);

    function go_to_profile (elm) {
      var url = 'master/User/profile';
      goToURL(url);
    }
  </script>

</body>

</html>
