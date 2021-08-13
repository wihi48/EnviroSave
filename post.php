<?php
$conn = new mysqli('localhost', 'root', '', 'ratingSystem');
if (isset($_POST['save'])) {

    $uID = $conn->real_escape_string($_POST['uID']);
    $ratedIndex = $conn->real_escape_string($_POST['ratedIndex']);
    $ratedIndex++;

    if (!$uID) {
        $conn->query("INSERT INTO stars (rateIndex) VALUES ('$ratedIndex')");
        $sql = $conn->query("SELECT id FROM stars ORDER BY id DESC LIMIT 1");
        $uData = $sql->fetch_assoc();
        $uID = $uData['id'];
    } else {
        $conn->query("UPDATE stars SET rateIndex='$ratedIndex' WHERE id='$uID'");
    }
    exit(json_encode(array('id' => $uID)));
}
$sql = $conn->query("SELECT id FROM stars");
$numR = $sql->num_rows;

$sql = $conn->query("SELECT SUM(rateIndex) AS total FROM stars");
$rData = $sql->fetch_array();
$total = $rData['total'];

$avg = $total / $numR;
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>EnviroSave - Post Page</title>
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
        <!-- Font Awesome icons (free version)-->
        <script src="https://use.fontawesome.com/releases/v5.15.3/js/all.js" crossorigin="anonymous"></script>
        <!-- Google fonts-->
        <link href="https://fonts.googleapis.com/css?family=Lora:400,700,400italic,700italic" rel="stylesheet" type="text/css" />
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800" rel="stylesheet" type="text/css" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="css/styles.css" rel="stylesheet" />
        <script src="https://kit.fontawesome.com/4d660a4408.js" crossorigin="anonymous"></script>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script>
            var ratedIndex = -1, uID = 0;
            $(document).ready(function () {
                resetStarColors();
                if (localStorage.getItem('ratedIndex') != null) {
                    setStars(parseInt(localStorage.getItem('ratedIndex')));
                    uID = localStorage.getItem('uID');
                }
                $('.fa-star').on('click', function () {
                    ratedIndex = parseInt($(this).data('index'));
                    localStorage.setItem('ratedIndex', ratedIndex);
                    saveToTheDB();
                });
                $('.fa-star').mouseover(function () {
                    resetStarColors();

                    var currentIndex = parseInt($(this).data('index'));

                    setStars(currentIndex);

                });
                $('.fa-star').mouseleave(function () {
                    resetStarColors();

                    if (ratedIndex != -1) {
                        setStars(ratedIndex);
                    }

                });
            });
            function saveToTheDB() {
                $.ajax({
                    url: "post.php",
                    method: "POST",
                    dataType: 'json',
                    data: {
                        save: 1,
                        uID: uID,
                        ratedIndex: ratedIndex
                    }, success: function (r) {
                        uID = r.id;
                        localStorage.setItem('uID', uID);
                    }
                });
            }
            function setStars(max) {
                for (var i = 0; i <= max; i++) {
                    $('.fa-star:eq(' + i + ')').css('color', 'orange');
                }
            }
            function resetStarColors() {
                $('.fa-star').css('color', 'black');
            }

        </script>
        <!--Comment System using PHP and Ajax-->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    </head>
    <body>
        <?php
        $msg = "";
        if (isset($_POST['upload'])) {
            $target = "assets/img/" . basename($_FILES['image']['name']);
            $db = mysqli_connect("localhost", "root", "", "photos");
            $image = $_FILES['image']['name'];
            $text = $_POST['text'];
            $sql = "INSERT INTO images (image, text) VALUES ('$image', '$text')";
            mysqli_query($db, $sql);

            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $msg = "Image uploaded Successfully";
            } else {
                $msg = "There was a problem uploading image";
            }
        }
        ?>
        <?php
        $sql1 = "SELECT image FROM images";
        $dbc = mysqli_connect("localhost", "root", "", "photos");
        $result1 = mysqli_query($dbc, $sql1) or die("Bad Insert: $sql1");
        ?>

        <!-- Navigation-->
        <nav class="navbar navbar-expand-lg navbar-light" id="mainNav">
            <div class="container px-4 px-lg-5">
                <a class="navbar-brand" href="index.html">EnviroSave</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                    Menu
                    <i class="fas fa-bars"></i>
                </button>
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav ms-auto py-4 py-lg-0">
                        <li class="nav-item"><a class="nav-link px-lg-3 py-3 py-lg-4" href="index.html">Home</a></li>
                        <li class="nav-item"><a class="nav-link px-lg-3 py-3 py-lg-4" href="about.html">About</a></li>
                        <li class="nav-item"><a class="nav-link px-lg-3 py-3 py-lg-4" href="post.php">Post Image</a></li>
                        <li class="nav-item"><a class="nav-link px-lg-3 py-3 py-lg-4" href="contact.html">Contact</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- Page Header-->
        <header class="masthead" style="background-image: url('assets/img/post-bg.jpg')">
            <div class="container position-relative px-4 px-lg-5">
                <div class="row gx-4 gx-lg-5 justify-content-center">
                    <div class="col-md-10 col-lg-8 col-xl-7">
                        <div class="site-heading">
                            <h1>Post Image</h1>
                            <span class="subheading">Capture the dangers in the environment</span>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- Post Content-->
        <article class="mb-4">
            <div class="container px-4 px-lg-5">
                <div class="row gx-4 gx-lg-5 justify-content-center">
                    <div class="col-md-10 col-lg-8 col-xl-7">
                        <h1>Image Gallery</h1>
                        <hr>
                        <h3>Places that need attention:</h3> <br>
                        <table>
                            <?php
                            $i = 0;
                            while ($row = mysqli_fetch_assoc($result1)) {
                                if ($i % 3 == 0) {
                                    echo"<tr>";
                                }
                                echo"<td>" . "<img src='assets/img/" . $row['image'] . "'width=\"100\" height=\"70\"></td>";
                                if ($i % 3 == 2) {
                                    echo"</tr>";
                                }
                                $i++;
                            }
                            ?>
                        </table> <br>

                        <h1>Images and Descriptions</h1>
                        <hr>

                        <div id="content">
                            <?php
                            $db = mysqli_connect("localhost", "root", "", "photos");
                            $sql = "SELECT * FROM images";
                            $result = mysqli_query($db, $sql);
                            while ($row = mysqli_fetch_array($result)) {
                                echo "<div id='img_div'>";
                                echo"<img src='assets/img/" . $row['image'] . "' width=\"300\" height=\"200\"> ";

                                echo"<p>" . $row['text'] . "</p>";
                                echo "</div>";
                            }
                            ?>

                            <h1>Post Image</h1>
                            <hr>

                            <form method="post" action="post.php" enctype="multipart/form-data">
                                <input type="hidden" name="size" value="1000000">
                                <div>
                                    <input type="file" name="image">

                                </div>
                                <div>
                                    <textarea name="text" cols="40" rows="4" placeholder="Say something about this image..."></textarea>
                                </div>
                                <div>
                                    <input type="submit" name="upload" value="Upload Image">
                                </div>
                            </form>
                        </div>
                        <div>
                            <p>
                                How would you rate your experience with this site?
                            </p>
                            <i class="fas fa-star" data-index='0'></i>
                            <i class="fas fa-star" data-index='1'></i>
                            <i class="fas fa-star" data-index='2'></i>
                            <i class="fas fa-star" data-index='3'></i>
                            <i class="fas fa-star" data-index='4'></i>
                            <br><br>
                            <?php echo round($avg, 2) ?>
                        </div>
                    </div>
                </div>
            </div>
            <!--Comment Section-->
            <br />
            <h2 align="center">Discussion Board</h2> <hr>
            <br />
            <div class="container">
                <form method="POST" id="comment_form">
                    <div class="form-group">
                        <input type="text" name="comment_name" id="comment_name" class="form-control" placeholder="Enter Name" />
                    </div> <br>
                    <div class="form-group">
                        <textarea name="comment_content" id="comment_content" class="form-control" placeholder="Enter Comment" rows="5"></textarea>
                    </div> <br>
                    <div class="form-group">
                        <input type="hidden" name="comment_id" id="comment_id" value="0" />
                        <input type="submit" name="submit" id="submit" class="btn btn-info" value="Submit" />
                    </div>
                </form>
                <span id="comment_message"></span>
                <br />
                <div id="display_comment"></div>
            </div>
        </article>
        <!-- Footer-->
        <footer class="border-top">
            <div class="container px-4 px-lg-5">
                <div class="row gx-4 gx-lg-5 justify-content-center">
                    <div class="col-md-10 col-lg-8 col-xl-7">
                        <ul class="list-inline text-center">
                            <li class="list-inline-item">
                                <a href="https://twitter.com/">
                                    <button style="border: 0px; background:transparent"><img src="assets/img/twitterLogo.png"></button>
                                </a>
                            </li>
                            <li class="list-inline-item">
                                <a href="https://facebook.com/">
                                    <button style="border: 0px; background:transparent"><img src="assets/img/facebookLogo.png"></button>
                                </a>
                            </li>
                            <li class="list-inline-item">
                                <a href="https://instagram.com/">
                                    <button style="border: 0px; background:transparent"><img src="assets/img/instagramLogo.png"></button>
                                </a>
                            </li>
                        </ul>
                        <div class="small text-center text-muted fst-italic">Copyright &copy; EnviroSave 2021</div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="js/scripts.js"></script>
    </body>
</html>

<!--Comment Section Stuff-->
<script>
            $(document).ready(function () {

                $('#comment_form').on('submit', function (event) {
                    event.preventDefault();
                    var form_data = $(this).serialize();
                    $.ajax({
                        url: "add_comment.php",
                        method: "POST",
                        data: form_data,
                        dataType: "JSON",
                        success: function (data)
                        {
                            if (data.error != '')
                            {
                                $('#comment_form')[0].reset();
                                $('#comment_message').html(data.error);
                                $('#comment_id').val('0');
                                load_comment();
                            }
                        }
                    })
                });

                load_comment();

                function load_comment()
                {
                    $.ajax({
                        url: "fetch_comment.php",
                        method: "POST",
                        success: function (data)
                        {
                            $('#display_comment').html(data);
                        }
                    })
                }

                $(document).on('click', '.reply', function () {
                    var comment_id = $(this).attr("id");
                    $('#comment_id').val(comment_id);
                    $('#comment_name').focus();
                });

            });
</script>
