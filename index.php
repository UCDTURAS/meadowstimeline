<?php
namespace meadows;
use meadows;
/**
 * Main page for meadows package
 *
 * @author daithi coombes <daithi.coombes@futureanalytics.ie>
 */

require_once('bootstrap.php');

$error = false;
if(isset($_FILES['image']) && $_FILES['image']['error']!=4){

    $origin = $_FILES['image']['tmp_name'];
    $dest = \FAC\Upload::getUnique($_FILES['image']['name']);

    if(!move_uploaded_file($origin, $dest)){
        $error = \FAC\Error::factory('Unable to upload image');
    }

    $file = (object) pathinfo($dest);
    $_POST['data']['image'] = $file->basename;
}

if(isset($_FILES['file']) && $_FILES['file']['error']!=4 && !$error){

    $origin = $_FILES['file']['tmp_name'];
    $dest = \FAC\Upload::getUnique($_FILES['file']['name']);

    if(!move_uploaded_file($origin, $dest)){
        $error = Error::factory('Unable to upload file');
    }

    $file = (object) pathinfo($dest);
    $_POST['data']['file'] = $file->basename;
}

if(isset($_POST['data']) && !$error){
    $id = $db->insert('Entry', $_POST['data']);
    if($id)
      $message = \FAC\Message::factory('Entry added successfully');
}

?>
<!DOCTYPE html>
<html lang="en-US" dir="ltr">
<head>
  <meta charset="utf-8" />
  <title>
   Timeline
  </title>
  <meta name="description" content="TimeMapper" />

  <!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
  <!--[if lt IE 9]>
    <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->

  <link rel="stylesheet" href="//assets.okfn.org/themes/okfn/okf-panel.css"/>

  <link rel="stylesheet" type="text/css" href="http://w.sharethis.com/button/css/buttons.4d4008011051a133a045fe92d3143ad4.css"/>
  <link rel="stylesheet" type="text/css" href="assets/css/normalize.css" />
  <link rel="stylesheet" type="text/css" href="assets/css/vicons-font.css" />
  <link rel="stylesheet" type="text/css" href="assets/css/buttons.css" />

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/leaflet.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/leaflet.js"></script>
  <!--[if lte IE 8]>
  <link rel="stylesheet" href="//npmcdn.com/leaflet@0.7.7/dist/leaflet.css" />
  <![endif]-->
  <link rel="stylesheet" href="//timemapper.okfnlabs.org//vendor/recline/vendor/leaflet.markercluster/MarkerCluster.css">
  <link rel="stylesheet" href="//timemapper.okfnlabs.org//vendor/recline/vendor/leaflet.markercluster/MarkerCluster.Default.css">
  <link rel="stylesheet" href="//timemapper.okfnlabs.org//vendor/leaflet.label/leaflet.label.css" />
  <link rel="stylesheet" href="//timemapper.okfnlabs.org//vendor/recline/vendor/timeline/css/timeline.css">
  <link rel="stylesheet" href="assets/vendor/recline/recline.css">
  <link rel="stylesheet" type="text/css" href="assets/vendor/bootstrap-3.3.5/dist/css/bootstrap.css">
  <link rel="stylesheet" type="text/css" href="assets/vendor/bootstrap-3.3.5/dist/css/bootstrap-theme.css">
  <!--<link rel="stylesheet" type="text/css" href="assets/vendor/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css">-->

  <link rel="stylesheet" href="assets/css/pikaday.css" type="text/css">
  <link rel="stylesheet" href="//timemapper.okfnlabs.org/css/style.css">
  <link rel="stylesheet" type="text/css" href="assets/css/base.css" />


<script src="https://www.mapquestapi.com/sdk/leaflet/v2.2/mq-map.js?key=QGI4AqAyfojnvQbci3mAHJA9G1PKkwDB"></script>

  <?php if(isset($_GET['noOverlay'])): ?>
    <script>
    var noOverlay = true;
    </script>
  <?php endif; ?>

</head>
<body>
    <?php if(!isset($_GET['noOverlay'])): ?>
    <div id="overlay">
        <p>Click to continue...</p>
        <span id="overlayPadding"></span>
        <img src="assets/images/overlay1-983x983.png" width="558" height="558" alt="init screen overlay">
    </div>
    <?php endif; ?>

    <div id="navigation">
        <div id="navigationInner">
            <ul>
                <li>
                    <button class="btn" data-toggle="modal" data-target="#tutorialModal" data-dismiss="modal" id="btnTutorialModal">
                      <i class="button__icon icon icon-play"></i>
                      Tutorial
                    </button>
                </li>
                <li>
                    <button class="btn" data-toggle="modal" data-target="#addEntryModal" data-dismiss="modal" id="btnAddEntryModal">
                      <i class="button__icon icon icon-plus"></i>
                      Add Entry
                    </button>
                </li>
                <li>
                    <button class="btn" data-toggle="modal" data-target="#aboutModal" data-dismiss="modal" id="btnAboutModal">
                      <i class="button__icon icon icon-clock"></i>
                      About
                    </button>
                </li>
            </ul>
            <div id="searchContainer">
                <input type="text" id="search_expr" data-provide="typeahead" autocomplete="off" placeholder="type to search...">
                <select id="searchCategory">
                  <option value="null">filter by category</option>
                  <option value="1">Infrastructure</option>
                  <option value="2">Development</option>
                  <option value="3">Housing</option>
                  <option value="4">Recreation</option>
                  <option value="5">Community Facilities</option>
                  <option value="6">Events</option>
                  <option value="7">Memories</option>
                  <option value="8">Transport</option>
                  <option value="9">Industry and Employment</option>
                </select>
                <input type="button" id="search_button" value="search" class="hidden" data-id="0">
            </div>
        </div>
    </div>

    <div class="container" id="messages">
      <?php \FAC\Message::printMessages(); ?>
    </div>

    <div class="container">
        <div class="container">
            <div class="content">

                <div class="data-views">
                    <div class="panes">
                        <div class="timeline-pane">
                            <div class="timeline"></div>
                        </div>
                        <div class="map-pane">
                            <div class="map"></div>
                        </div>
                    </div>
                </div>

                <div class="loading js-loading" ><i class="icon-spinner icon-spin icon-large"></i> Loading data...</div>
            </div> <!-- /content -->
        </div>
    </div> <!-- / container-fluid -->

    <div class="modal fade" id="addEntryModal" tabindex="-1" role="dialog" aria-labelledby="btnAddEntryModal">
      <div class="modal-dialog">

        <form class="form-horizontal" id="addEntryForm" method="post" enctype="multipart/form-data" oncontextmenu="return false;" action="?noOverlay=1">
          <div class="modal-content">

            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Add Entry</h4>
            </div>

            <div class="modal-body">
              <div id="map_canvas"></div>
              <div class="form-group">
                <label for="title" class="col-sm-4 control-label">Title</label>
                <div class="col-sm-8">
                  <input type="text" name="data[title]" class="form-control" id="title" placeholder="Enter Title" required>
                </div>
              </div>
              <div class="form-group">
                <label for="description" class="col-sm-4 control-label">Description</label>
                <div class="col-sm-8">
                  <textarea class="form-control" rows="3" name="data[description]" placeholder="Enter Description" id="description"></textarea>
                </div>
              </div>
              <div class="form-group">
                <label for="latitude" class="col-sm-4 control-label">Latitude</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" name="data[lat]" id="latitude" placeholder="Enter Latitude" required>
                </div>
              </div>
              <div class="form-group">
                <label for="longitude" class="col-sm-4 control-label">Longitude</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" name="data[lng]" id="longitude" placeholder="Enter Longitude" required>
                </div>
              </div>
              <div class="form-group">
                <label for="start" class="col-sm-4 control-label">Start Date</label>
                <div class="col-sm-8">
                  <div class="input-group date dtp-container">
                    <input data-format="dd/MM/yyyy hh:mm:ss" type="text" class="form-control datetime datetimepicker" name="data[start]" id="start" placeholder="Enter Start Date" required>
                    <span class="input-group-addon add-on"><i class="glyphicon glyphicon-calendar" data-time-icon="icon-time" data-date-icon="icon-calendar"></i></span>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label for="end" class="col-sm-4 control-label">End Date</label>
                <div class="col-sm-8">
                  <div class="input-group date dtp-container">
                    <input data-format="dd/MM/yyyy hh:mm:ss" type="text" class="form-control datetime datetimepicker" name="data[end]" id="end" placeholder="Enter End Date" required>
                    <span class="input-group-addon add-on"><i class="glyphicon glyphicon-calendar" data-time-icon="icon-time" data-date-icon="icon-calendar"></i></span>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label for="category" class="col-sm-4 control-label">Category</label>
                <div class="col-sm-8">
                  <select class="form-control" id="category" name="data[category]">
                    <option>Select A Category</option>
                    <option value="1">Infrastructure</option>
                    <option value="2">Development</option>
                    <option value="3">Housing</option>
                    <option value="4">Recreation</option>
                    <option value="5">Community Facilities</option>
                    <option value="6">Events</option>
                    <option value="7">Memories</option>
                    <option value="8">Transport</option>
                    <option value="9">Industry and Employment</option>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label for="file" class="col-sm-4 control-label">Upload File</label>
                <div class="col-sm-8">
                  <input type="file" name="file" id="file">
                </div>
              </div>
              <div class="form-group">
                <label for="image" class="col-sm-4 control-label">Upload Image</label>
                <div class="col-sm-8">
                  <input type="file" name="image" id="image">
                </div>
              </div>
              <div class="form-group">
              </div>
            </div>

            <div class="modal-footer">
              <input type="button" class="btn btn-default" data-dismiss="modal" value="Close">
              <input type="submit" class="btn btn-primary" value="Save">
            </div>

          </div><!-- .modal-content -->
        </form>

      </div><!-- .modal-dialog -->
    </div><!-- .modal -->


    <div class="modal fade" id="tutorialModal" tabindex="-1" role="dialog" aria-labelledby="btnTutorialModal">
      <div class="modal-dialog">
        <iframe src="https://player.vimeo.com/video/127707850?byline=0&amp;portrait=0" allowfullscreen width="700" height="393"></iframe>
      </div>
    </div>

    <div class="modal fade" id="aboutModal" tabindex="-1" role="dialog" aria-labelledby="btnAboutModal">
      <div class="modal-dialog">
        <img src="assets/images/overlay2-558x558.png" alt="About details">
      </div>
    </div>

    <footer class="footer">
      <div class="container-fluid">
        <img src="assets/images/european-union-logo-75x50.jpg" alt="logo European Union">

        <span>
          <span class="stButton" style="text-decoration:none;color:#000000;display:inline-block;cursor:pointer;top:-13px">
            <a href="http://www.turas-cities.org/urban_regions/Nottingham/en" target="new"><img src="/assets/images/nottingham-logo-32x32.png" alt="Logo Nottingham City"></a>
          </span>
        </span>
        <span class="st_twitter_large">
          <span class="stButton" style="text-decoration:none;color:#000000;display:inline-block;cursor:pointer;">
            <a href="https://twitter.com/UniofNottingham" target="new"><span style="background-image: url(http://w.sharethis.com/images/twitter_32.png);" class="stLarge"></span></a>
          </span>
        </span>
        <span class="st_email_large">
          <span class="stButton" style="text-decoration:none;color:#000000;display:inline-block;cursor:pointer;">
            <a href="mailto:lucelia.rodrigues@nottingham.ac.uk"><span style="background-image: url(http://w.sharethis.com/images/email_32.png);" class="stLarge"></span></a>
          </span>
        </span>
      </div>
    </footer>

    <script type="text/javascript">
    var VIZDATA = {
        "licenses":[{
            "type":"cc-by",
            "name":"Creative Commons Attribution",
            "version":"3.0","url":"//creativecommons.org/licenses/by/3.0/"
        }],
        "resources":[{
            "backend":"csv",
            //"backend":"json",
            "url":"ajax.php"
            //"url": "//docs.google.com/spreadsheets/d/16yfioqfQdPLfTCpdOt0xTUKIr1hHlIZZBA16PovP6cY/edit#gid=901259027"
            //"url": "//docs.google.com/spreadsheets//u/0/d/16yfioqfQdPLfTCpdOt0xTUKIr1hHlIZZBA16PovP6cY/export?format=csv&id=16yfioqfQdPLfTCpdOt0xTUKIr1hHlIZZBA16PovP6cY&gid=0"
        }],
        "title":"Medieval Philosophers - Timeliner",
        "tmconfig":{
            "viewtype":"timemap",
            "dayfirst":true,
            "startfrom":"start"
        },
        "owner":"anon",
        "name":"gocu1r-medieval-philosophers-timeliner",
        "_last_modified":"2015-02-27T16:15:06.644Z",
        "_created":"2015-02-27T16:15:06.644Z"
    };
    // define global TM object and set some config
    var TM = TM || {};
    TM.locals = {
      currentUser: ""
    };
    </script>

    <script src="assets/vendor/jquery-2.1.4.min.js"></script>
    <script src="assets/vendor/moment-with-locales-2.8.0.min.js"></script>
    <script src="assets/vendor/bootstrap-3.3.5/js/transition.js"></script>
    <script src="assets/vendor/bootstrap-3.3.5/js/collapse.js"></script>
    <script src="assets/vendor/bootstrap-3.3.5/dist/js/bootstrap.min.js"></script>

    <script src="//maps.googleapis.com/maps/api/js?v=3.exp&amp;signed_in=true&amp;libraries=places"></script>
    <script type="text/javascript" src="//timemapper.okfnlabs.org//vendor/recline/vendor/underscore/1.4.4/underscore.js"></script>
    <script type="text/javascript" src="//timemapper.okfnlabs.org//vendor/recline/vendor/backbone/1.0.0/backbone.js"></script>
    <script type="text/javascript" src="//timemapper.okfnlabs.org//vendor/recline/vendor/moment/2.0.0/moment.js"></script>
    <script type="text/javascript" src="//timemapper.okfnlabs.org//vendor/recline/vendor/mustache/0.5.0-dev/mustache.js"></script>
    <script type="text/javascript" src="//timemapper.okfnlabs.org//vendor/recline/vendor/leaflet.markercluster/leaflet.markercluster.js"></script>
    <script type="text/javascript" src="//timemapper.okfnlabs.org//vendor/leaflet.label/leaflet.label.js"></script>
    <script src="//assets.okfn.org/themes/okfn/collapse.min.js" type="text/javascript"></script>
    <script src="//assets.okfn.org/themes/okfn/okf-panel.js" type="text/javascript"></script>
    <script type="text/javascript" src="//okfnlabs.org/recline.backend.gdocs/backend.gdocs.js"></script>
    <script type="text/javascript" src="assets/vendor/recline/recline.js"></script>

    <script type="text/javascript" src="assets/js/pikaday.js"></script>
    <script type="text/javascript" src="assets/js/jquery.autocomplete.js"></script>
    <script type="text/javascript" src="assets/js/timeline.js"></script>
    <script type="text/javascript" src="assets/js/view.js"></script>
    <script type="text/javascript" src="assets/js/meadows.js"></script>

    <script type="text/javascript">
      var _gaq = _gaq || [];
      _gaq.push(['_setAccount', 'UA-33874954-2']);
      _gaq.push(['_trackPageview']);

      (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? '//ssl' : '//www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
      })();
    </script>
</body>
</html>
