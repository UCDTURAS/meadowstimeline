<?php
/**
 * @author daithi coombes <daithi.coombes@futureanalytics.ie>
 */
session_start();

require_once('login.php');
require_once('../bootstrap.php');

$db = \FAC\Model::factory();

$categories = array('',
  'Infrastructure',
  'Development',
  'Housing',
  'Recreation',
  'Community Facilities',
  'Events',
  'Memories',
  'Transport',
  'Industry and Employment',
);

//delete a record?
if(isset($_REQUEST['action']) && $_REQUEST['action']=='delete' && isset($_REQUEST['id'])){

    $pdo = $db->getDb();
    $sql = "DELETE FROM Entry WHERE id=:id";

    $sth = $pdo->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $sth->execute(array(':id'=>$_REQUEST['id']));
}


//delete an image?
if(isset($_REQUEST['action']) && $_REQUEST['action']=='deleteImage' && isset($_REQUEST['id'])){

    $pdo = $db->getDB();
    $sql = "UPDATE Entry SET image=NULL WHERE id=:id";

    $sth = $pdo->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $sth->execute(array(':id'=>$_REQUEST['id']));

    if($_GET['file']!="")
      while(file_exists("../uploads/{$_GET['file']}"))
        unlink("../uploads/{$_GET['file']}");
}

//delete a file?
if(isset($_GET['action']) && $_GET['action']=='deleteFile' && isset($_GET['id'])){

    $pdo = $db->getDB();
    $sql = "UPDATE Entry SET file=NULL WHERE id=:id";

    $sth = $pdo->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $sth->execute(array(':id'=>$_GET['id']));

    if($_GET['file']!="")
      while(file_exists("../uploads/{$_GET['file']}"))
        unlink("../uploads/{$_GET['file']}");
}


//uploading image
$error = false;
if(isset($_FILES['image'])){

    if(\FAC\Upload::validateUpload($_FILES['image'])){
      $origin = $_FILES['image']['tmp_name'];
      $dest = \FAC\Upload::getUnique($_FILES['image']['name']);

      if(!move_uploaded_file($origin, $dest)){
          $error = \FAC\Error::factory('Unable to upload image');
      }

      $file = (object) pathinfo($dest);
      $_POST['data']['image'] = $file->basename;

      if($_POST['data'] && $_FILES['image']['tmp_name'] && !$error){

          $id = $_POST['data']['id'];
          $filename = $file->basename;
          $pdo = $db->getDB();
          $sql = "UPDATE Entry SET image=? WHERE id=?";

          $sth = $pdo->prepare($sql);
          $sth->execute(array($filename, $id));

          $error = $sth->errorInfo();
      }
    }
    else $error = true;
}

//uploading file
if(isset($_FILES['file']) && !$error){

    $origin = $_FILES['file']['tmp_name'];
    $dest = \FAC\Upload::getUnique($_FILES['file']['name']);

    if(!move_uploaded_file($origin, $dest)){
        $error = Error::factory('Unable to upload file');
    }

    $file = (object) pathinfo($dest);
    $_POST['data']['file'] = $file->basename;

    if($_POST['data'] && $_FILES['file']['tmp_name'] && !$error){

        $id = $_POST['data']['id'];
        $filename = $file->basename;
        $pdo = $db->getDB();
        $sql = "UPDATE Entry SET file=? WHERE id=?";

        $sth = $pdo->prepare($sql);
        $sth->execute(array($filename, $id));

        $error = $sth->errorInfo();
    }
}


//get entries
$entries = $db->query("SELECT * FROM Entry");

?>
<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">

  <title>Meadows - Admin</title>
  <meta name="description" content="Meadows admin">
  <meta name="author" content="daithi coombes <daithi.coombes@futureanalytics.ie>">

  <!-- bootstrap -->
  <link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.min.css" rel="stylesheet">
  <script src="http://code.jquery.com/jquery-2.0.3.min.js"></script>
  <script src="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/js/bootstrap.min.js"></script>

  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.8.1/bootstrap-table.min.css">

  <!-- Latest compiled and minified JavaScript -->
  <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.8.1/bootstrap-table.min.js"></script>

  <!-- x-editable (bootstrap version) -->
  <link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.4.6/bootstrap-editable/css/bootstrap-editable.css" rel="stylesheet"/>
  <script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.4.6/bootstrap-editable/js/bootstrap-editable.min.js"></script>

  <style type="text/css">
    a.delete{
      width: 25px;
      height: 25px;
    }
    a.delete img:hover{
      background-color: #fff;
    }
  </style>

  <script type="text/javascript">
      $(document).ready(function(){

          //delete record
          $('a.delete').click(function(){

            var id = $(this).data('id'),
                title = $(this).data('title');

            if(confirm('Are you sure you want to delete:\n\t'+title))
                window.location.href = '?action=delete&id='+id;

            return false;
          })

          //delete image
          $('a.delete-image').click(function(){

              var id = $(this).data('id'),
                  file = $(this).data('file'),
                  title = $(this).data('title');

              if(confirm('Are you sure you want to delete the image for:\n\t'+title))
                window.location.href = '?action=deleteImage&id='+id+'&file='+file;

              return false;
          })

          //delete file
          $('a.delete-file').click(function(){

              var id = $(this).data('id'),
                file = $(this).data('file'),
                title = $(this).data('title');

              if(confirm('Are you sure you want to delete the file for:\n\t'+title))
                window.location.href = '?action=deleteFile&id='+id+'&file='+file;

              return false;
          })
      })
  </script>

  <!--[if lt IE 9]>
  <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
</head>

<body>
    <div class="container">
        <nav class="navbar">
            <div class="navbar-inner">
                <a class="brand" href="#">Meadows Admin</a>
                <ul class="nav">
                    <li class="active"><a href="?logout=1">logout</a></li>
                </ul>
            </div><!--/.container-fluid -->
        </nav>
    </div>

    <div class="container">
        <?php if(count(\FAC\Error::getErrors(false))): ?>
          <div class="row">
            <div class="col-xs-12">
              <?php foreach(\FAC\Error::getErrors() as $err): ?>
                <div class="alert alert-danger" role="alert"><?php echo $err; ?></div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>
        <div class="row">
            <div class="col-xs-12">

                <table id="entries" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th data-sortable="true">id</th>
                            <th data-sortable="true">title</th>
                            <th data-sortable="true">description</th>
                            <th data-sortable="true">category</th>
                            <th data-sortable="true">start</th>
                            <th data-sortable="true">end</th>
                            <th data-sortable="true">lat</th>
                            <th data-sortable="true">lng</th>
                            <th data-sortable="true">image</th>
                            <th data-sortable="true">file</th>
                            <th data-sortable="true">created</th>
                            <th data-sortable="true">updated</th>
                            <th>actions</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>id</th>
                            <th>title</th>
                            <th>description</th>
                            <th>start</th>
                            <th>end</th>
                            <th>lat</th>
                            <th>lng</th>
                            <th>images</th>
                            <th>file</th>
                            <th>created</th>
                            <th>updated</th>
                            <th>actions</th>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php foreach($entries as $entry): ?>
                            <tr>
                                <td><?php echo $entry['id']; ?></td>
                                <td><a href="#" class="editable" data-type="text" data-pk="<?php echo $entry['id']; ?>-title" data-url="ajax.php" data-title="Enter "><?php echo $entry['title']; ?></a></td>
                                <td><a href="#" class="editable" data-type="text" data-pk="<?php echo $entry['id']; ?>-description" data-url="ajax.php" data-title="Enter "><?php echo $entry['description']; ?></a></td>
                                <td><a href="#" class="editable category" data-type="select" data-pk="<?php echo $entry['id']; ?>-category" data-url="ajax.php" data-title="Enter " value="<?php echo $entry['category']; ?>"><?php echo $categories[$entry['category']]; ?></a></td>
                                <td><a href="#" class="editable" data-type="text" data-pk="<?php echo $entry['id']; ?>-start" data-url="ajax.php" data-title="Enter "><?php echo $entry['start']; ?></a></td>
                                <td><a href="#" class="editable" data-type="text" data-pk="<?php echo $entry['id']; ?>-end" data-url="ajax.php" data-title="Enter "><?php echo $entry['end']; ?></a></td>
                                <td><a href="#" class="editable" data-type="text" data-pk="<?php echo $entry['id']; ?>-lat" data-url="ajax.php" data-title="Enter "><?php echo $entry['lat']; ?></a></td>
                                <td><a href="#" class="editable" data-type="text" data-pk="<?php echo $entry['id']; ?>-lng" data-url="ajax.php" data-title="Enter "><?php echo $entry['lng']; ?></a></td>
                                <td>
                                    <?php if($entry['image']): ?>
                                      <img src="<?php echo \FAC\Media::getUpload($entry['image']); ?>" width="80">
                                      <a href="#" data-title="<?php echo $entry['title']; ?>" data-file="<?php echo $entry['image']; ?>" data-id="<?php echo $entry['id']; ?>" class="delete-image" title="Delete image for <?php echo $entry['title']; ?>">
                                          <img src="../assets/images/deletered-15x15.png">
                                      </a>
                                    <?php else: ?>
                                      <form method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                                          <input type="hidden" name="data[id]" value="<?php echo $entry['id']; ?>">
                                          <input type="file" name="image">
                                          <input type="submit" value="Upload">
                                      </form>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($entry['file']): ?>
                                        <a href="../uploads/<?php echo $entry['file']; ?>"><?php echo $entry['file']; ?></a>
                                        <a href="#" data-title="<?php echo $entry['title']; ?>" data-file="<?php echo $entry['file']; ?>" data-id="<?php echo $entry['id']; ?>" class="delete-file" title="Delete file for <?php echo
                                        $entry['title']; ?>">
                                            <img src="../assets/images/deletered-15x15.png">
                                        </a>
                                    <?php else: ?>
                                        <form method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                                            <input type="hidden" name="data[id]" value="<?php echo $entry['id']; ?>">
                                            <input type="file" name="file">
                                            <input type="submit" value="Upload">
                                        </form>
                                    <?php endif; ?>
                                </td>
                                <td><a href="#" class="editable" data-type="text" data-pk="<?php echo $entry['id']; ?>-created" data-url="ajax.php" data-title="Enter "><?php echo $entry['created']; ?></a></td>
                                <td><a href="#" class="editable" data-type="text" data-pk="<?php echo $entry['id']; ?>-updated" data-url="ajax.php" data-title="Enter "><?php echo $entry['updated']; ?></a></td>
                                <td><a href="#" data-title="<?php echo $entry['title']; ?>" data-id="<?php echo $entry['id']; ?>" class="delete" title="Delete record '<?php echo $entry['title']; ?>'">
                                    <img src="../assets/images/deletered-25x25.png" border="0">
                                </a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $.fn.editable.defaults.mode = 'inline';

        $(document).ready(function(){
            $('.editable').each(function(i, el){

                //category select
                if($(this).hasClass('category'))
                  $(this).editable({
                    source: [
                        {value: 1, text: 'Infrastructure'},
                        {value: 2, text: 'Development'},
                        {value: 3, text: 'Housing'},
                        {value: 4, text: 'Recreation'},
                        {value: 5, text: 'Community Facilities'},
                        {value: 6, text: 'Events'},
                        {value: 7, text: 'Memories'},
                        {value: 8, text: 'Transport'},
                        {value: 9, text: 'Industry and Employment'},
                    ]
                  })

                //default text
                else
                  $(this).editable();
            });
        });
    </script>
</body>
</html>
-l \
