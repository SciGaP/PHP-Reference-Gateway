<?php
/**
 * Allow users to log in or create a new account
 */
session_start();
include 'utilities.php';

connect_to_id_store();
?>

<html>
<head>
    <title>PHP Reference Gateway</title>
  
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
</head>


<body>
    
<nav class="navbar navbar-default navbar-static-top" role="navigation">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#">PHP Reference Gateway</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li class="disabled"><a href="home.php">Home</a></li>
        <li class="disabled"><a href="create_experiment.php">Create experiment</a></li>
        <li class="disabled"><a href="manage_experiments.php">Manage experiments</a></li>    
      </ul>
      
      <ul class="nav navbar-nav navbar-right">
        <li><a href="create_account.php">Create an account</a></li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>
    
<div class="container">

    <h3>Login</h3>





<?php

if (form_submitted())
{
    $username = $_POST['username'];
    $passwordHash = md5($_POST['password']);

    if (id_matches_db($username, $passwordHash))
    {
        store_id_in_session($username, $passwordHash);
        print_success_message('Login successful!');
        redirect('home.php');
    }
    else
    {
        print_error_message('Invalid username or password. Please try again.');
    }
}

?>


    <form action="<?php echo $_SERVER['PHP_SELF']?>" method="post" role="form">
        <div class="form-group">
            <label class="sr-only" for="username">Username</label>
            <input type="text" class="form-control" name="username" placeholder="Username">
        </div>
        <div class="form-group">
            <label class="sr-only" for="password">Password</label>
            <input type="password" class="form-control"  name="password" placeholder="Password">
        </div>
        <input name="Submit" type="submit" class="btn btn-primary" value="Submit">
    </form>




<!--<a href="create_account.php">Create an account</a>-->
</div>
</body>
</html>