<?php

//header.php

include('values.php');

session_start();

if(!isset($_SESSION["admin_id"]))
{
  header('location:login.php');
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Pannel</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.css">
  <link rel="stylesheet" href="../css/login.css">
  <!-- Core theme CSS (includes Bootstrap) -->
  <link href="../css/styles.css" rel="stylesheet" />
</head>

<body>
  <div class="header container-fluid">
    <div class="container">
      <div class="l-t-l">
        <div class="snsinstitute-logo">
          <img src="" alt="">
        </div>
        <div class="dept-text">
          <span>Head of the Department Portal</span><br>
          <br>
          <span class="att">ATTENDANCE MANAGEMENT SYSTEM</span>
        </div>
        <div class="snsce-logo">
          <img src="" alt="">
        </div>
      </div>
    </div>
  </div>
