<?php
session_start();
require_once 'LOGIN/components/db_connect.php';

// it will never let you open index (login) page if session is set
if (isset($_SESSION['user']) != "") {
    header("Location: LOGIN/home.php");
    exit;
}
if (isset($_SESSION['adm']) != "") {
    header("Location: LOGIN/dashboard.php"); // redirects to home.php
}

$error = false;
$email = $password = $emailError = $passError = '';

if (isset($_POST['btn-login'])) {

    // prevent sql injections/ clear user invalid inputs
    $email = trim($_POST['email']);
    $email = strip_tags($email);
    $email = htmlspecialchars($email);

    $pass = trim($_POST['pass']);
    $pass = strip_tags($pass);
    $pass = htmlspecialchars($pass);

    if (empty($email)) {
        $error = true;
        $emailError = "Please enter your email address.";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = true;
        $emailError = "Please enter valid email address.";
    }

    if (empty($pass)) {
        $error = true;
        $passError = "Please enter your password.";
    }

    // if there's no error, continue to login
    if (!$error) {

        $password = hash('sha256', $pass); // password hashing

        $sql = "SELECT id, first_name, password, status FROM users WHERE email = '$email'";
        $result = mysqli_query($connect, $sql);
        $row = mysqli_fetch_assoc($result);
        $count = mysqli_num_rows($result);
        if ($count == 1 && $row['password'] == $password) {
            if ($row['status'] == 'adm') {
                $_SESSION['adm'] = $row['id'];
                header("Location: LOGIN/dashboard.php");
            } else {
                $_SESSION['user'] = $row['id'];
                header("Location: LOGIN/home.php");
            }
        } else {
            $errMSG = "Incorrect Credentials, Try again...";
        }
    }
}

mysqli_close($connect);
?>
<?php 
require_once 'actions/db_connect.php';

$sql1 = "SELECT * FROM dishes order by name asc" ;
$result1 = mysqli_query($connect1 ,$sql1);
$tbody1=''; //this variable will hold the body for the table
if(mysqli_num_rows($result1)  > 0) {     
    while($row1 = mysqli_fetch_array($result1, MYSQLI_ASSOC)){         
        $tbody1 .= "<tr>
            <td><img class='img-thumbnail' src='pictures/" .$row['picture']."'</td>
            <td>" .$row['name']."</td>
            <td>" .$row['price']."</td>
            <td><a href='update.php?id=" .$row['id']."'><button class='btn btn-primary btn-sm' type='button'>Edit</button></a>
            <a href='delete.php?id=" .$row['id']."'><button class='btn btn-danger btn-sm' type='button'>Delete</button></a></td>
            </tr>";
    };
} else {
    $tbody1 =  "<tr><td colspan='5'><center>No Data Available </center></td></tr>";
}

mysqli_close($connect1);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>PHP CRUD</title>
        <?php require_once 'components/boot.php'?>
        <style type="text/css">
            .manageProduct {           
                margin: auto;
            }
            .img-thumbnail {
                width: 70px !important;
                height: 70px !important;
            }
            td {          
                text-align: left;
                vertical-align: middle;
            }
            tr {
                text-align: center;
            }
        </style>
    </head>
    <body>
    <div class="container">
        <form class="w-75" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" autocomplete="off">
            <h2>Login</h2>
            <hr />
            <?php
            if (isset($errMSG)) {
                echo $errMSG;
            }
            ?>

            <input type="email" autocomplete="off" name="email" class="form-control" placeholder="Your Email" value="<?php echo $email; ?>" maxlength="40" />
            <span class="text-danger"><?php echo $emailError; ?></span>

            <input type="password" name="pass" class="form-control" placeholder="Your Password" maxlength="15" />
            <span class="text-danger"><?php echo $passError; ?></span>
            <hr />
            <button class="btn btn-block btn-primary" type="submit" name="btn-login">Sign In</button>
            <hr />
            <a href="LOGIN/register.php">Not registered yet? Click here</a>
        </form>
    </div>
        <div class="manageProduct w-75 mt-3">    
            <div class='mb-3'>
                <a href= "create.php"><button class='btn btn-primary'type="button" >Add product</button></a>
            </div>
            <p class='h2'>Meals</p>
            <table class='table table-striped'>
                <thead class='table-success'>
                    <tr>
                        <th>Picture</th>
                        <th>Name</th>
                        <th>price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?= $tbody1;?>
                </tbody>
            </table>
            <table>
            <tr>
                        <td><button class='btn btn-success' type="submit">Insert Product</button></td>
                        <td><a href="index.php"><button class='btn btn-warning' type="button">Home</button></a></td>
                    </tr>
        </table>
        </div>
    </body>
</html>