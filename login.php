<?php

//login.php

include("admin/values.php");

session_start();

if(isset($_SESSION["dept_id"]))
{
  header('location:index.php');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Advisor Login</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="./css/login.css">
</head>

<body>
  <div class="header container-fluid">
    <div class="container">
      <div class="l-t-l">
        <div class="snsinstitute-logo">
          <img src="" alt="">
        </div>
        <div class="dept-text">
          <span>Class Advisors or Incharge Portal</span><br>
          <br>
          <span class="att">ATTENDANCE MANAGEMENT SYSTEM</span>
        </div>
        <div class="snsce-logo">
          <img src="" alt="">
        </div>
      </div>
    </div>
  </div>
  <div class="container">
    <div class="row">
      <div class="col-md-4">
      </div>
      <div class="col-md-4 mt-20px">
        <div class="card">
          <div class="card-header">Advisor Login</div>
          <div class="card-body">
            <form method="post" id="advisor_login_form">
              <div class="form-group">
                <label>Enter Email Address</label>
                <input type="text" name="advisor_email" id="advisor_email" class="form-control" />
                <span id="error_advisor_email" class="text-danger"></span>
              </div>
              <div class="form-group">
                <label>Enter Password</label>
                <input type="password" name="advisor_password" id="advisor_password" class="form-control" />
                <span id="error_advisor_password" class="text-danger"></span>
              </div>
              <div class="form-group">
                <input type="submit" name="advisor_login" id="advisor_login" class="btn btn-info" value="Login" />
              </div>
              <div class="form-group">
              <label>Username : advisor@gmail.com</label> <br>
              <label>Password : password</label>
              </div>
            </form>
          </div>
        </div>
        <div style="text-align:center;margin-top:10px">
          <a style="color:black;text-decoration: underline;" href="./admin/login.php">Switch to HOD Portal</a>
        </div>
      </div>
      <div class="col-md-4">
      </div>
    </div>
  </div>
  <script>
    $(document).ready(function () {
      $('#advisor_login_form').on('submit', function (event) {
        event.preventDefault();
        $.ajax({
          url: "check_advisor_login.php",
          method: "POST",
          data: $(this).serialize(),
          dataType: "json",
          beforeSend: function () {
            $('#advisor_login').val('Validate...');
            $('#advisor_login').attr('disabled', 'disabled');
          },
          success: function (data) {
            if (data.success) {
              location.href = "./index.php";
            }
            if (data.error) {
              $('#advisor_login').val('Login');
              $('#advisor_login').attr('disabled', false);
              if (data.error_advisor_email != '') {
                $('#error_advisor_email').text(data.error_advisor_email);
              }
              else {
                $('#error_advisor_email').text('');
              }
              if (data.error_advisor_password != '') {
                $('#error_advisor_password').text(data.error_advisor_password);
              }
              else {
                $('#error_advisor_password').text('');
              }
            }
          }
        });
      });
    });
  </script>
</body>

</html>
