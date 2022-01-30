<?php

// $connect = new PDO("mysql:host=localhost;dbname=attendance","root","");

$host = 'gokul-demo-mysql.mysql.database.azure.com';
$username = 'Gokul';
$password = 'Admin@123';
$db_name = 'attendance';

//Initializes MySQLi
$connect = mysqli_init();

mysqli_ssl_set($connect,NULL,NULL, "./DigiCertGlobalRootCA.crt.pem", NULL, NULL);

// Establish the connection
mysqli_real_connect($connect, 'gokul-demo-mysql.mysql.database.azure.com', 'Gokul', 'Admin@123', 'attendance', 3306, NULL, MYSQLI_CLIENT_SSL);

//If connection failed, show the error
if (mysqli_connect_errno())
{
    die('Failed to connect to MySQL: '.mysqli_connect_error());
}

date_default_timezone_set("Asia/Calcutta");

function get_total_records($connect, $table_name, $final_query)
{
	$query = "SELECT * FROM $table_name";
	// $statement = $connect->prepare($query);
	// $statement->execute();
	// return $statement->rowCount();

	$statement  = mysqli_query($connect, $final_query);
	return mysqli_num_rows($statement);
}

function load_dept_list($connect)
{
	$query = "
	SELECT * FROM tbl_department ORDER BY dept_year ASC
	";
	$statement = mysqli_query($connect, $query);
	// $statement = $connect->prepare($query);
	// $statement->execute();
	// $result = $statement->fetchAll();
	$output = '';
	// foreach($result as $row)
	// {
	// 	$output .= '<option value="'.$row["dept_id"].'">'.$row["dept_year"].' Year '.$row["dept_name"].'</option>';
	// }
	while($row = mysqli_fetch_assoc($statement)){
    	$output .= '<option value="'.$row["dept_id"].'">'.$row["dept_year"].' Year '.$row["dept_name"].'</option>';
	}
	return $output;
}

function load_advisor_dept($connect,$advisor_dept_val)
{
	// $query = "
	// SELECT * FROM tbl_department ORDER BY dept_year ASC
	// ";
	// $statement = $connect->prepare($query);
	// $statement->execute();
	// $result = $statement->fetchAll();
	// $output = '';
	// foreach($result as $row)
	// {
	// 	if($row["dept_id"] == $advisor_dept_val){
	// 	$output .= '<option value="'.$row["dept_id"].'">'.$row["dept_year"].' Year '.$row["dept_name"].'</option>';
	// 	}
	// }
	$query = "
	SELECT * FROM tbl_department ORDER BY dept_year ASC
	";
	$statement = mysqli_query($connect, $query);
	$output = '';
	while($row = mysqli_fetch_assoc($statement)){
    	if($row["dept_id"] == $advisor_dept_val){
			$output .= '<option value="'.$row["dept_id"].'">'.$row["dept_year"].' Year '.$row["dept_name"].'</option>';
		}
	}
	return $output;
}

function get_attendance_percentage($connect, $student_id)
{
	// $query = "
	// SELECT 
	// 	ROUND((SELECT COUNT(*) FROM tbl_attendance 
	// 	WHERE attendance_status = 1 
	// 	AND student_id = '".$student_id."') 
	// * 100 / COUNT(*)) AS percentage FROM tbl_attendance 
	// WHERE student_id = '".$student_id."'
	// ";

	// $statement = $connect->prepare($query);
	// $statement->execute();
	// $result = $statement->fetchAll();
	// foreach($result as $row)
	// {
	// 	if($row["percentage"] > 0)
	// 	{
	// 		return $row["percentage"] . '%';
	// 	}
	// 	else
	// 	{
	// 		return 'NA';
	// 	}
	// }

	$query = "
	SELECT 
		ROUND((SELECT COUNT(*) FROM tbl_attendance 
		WHERE attendance_status = 1 
		AND student_id = '".$student_id."') 
	* 100 / COUNT(*)) AS percentage FROM tbl_attendance 
	WHERE student_id = '".$student_id."'
";
$statement = mysqli_query($connect, $query);
while($row = mysqli_fetch_assoc($statement)){
    if($row["percentage"] > 0)
		{
			return $row["percentage"] . '%';
		}
		else
		{
			return 'NA';
		}
}
}

function Get_student_name($connect, $student_id)
{
	// $query = "
	// SELECT student_name FROM tbl_student 
	// WHERE student_id = '".$student_id."'
	// ";

	// $statement = $connect->prepare($query);

	// $statement->execute();

	// $result = $statement->fetchAll();

	// foreach($result as $row)
	// {
	// 	return $row["student_name"];
	// }

	$query = "
	SELECT student_name FROM tbl_student 
	WHERE student_id = '".$student_id."'
	";

	$statement = mysqli_query($connect, $query);
    while($row = mysqli_fetch_assoc($statement)){
        return $row["student_name"];
    }
}

function Get_student_dept_name($connect, $student_id)
{
	$query = "
	SELECT tbl_grade.grade_name FROM tbl_student 
	INNER JOIN tbl_grade 
	ON tbl_grade.grade_id = tbl_student.student_grade_id 
	WHERE tbl_student.student_id = '".$student_id."'
	";
	$statement = mysqli_query($connect, $query);
    while($row = mysqli_fetch_assoc($statement)){
        return $row["dept_name"];
    }
	// foreach($result as $row)
	// {
	// 	return $row['dept_name'];
	// }
}

function Get_student_advisor_name($connect, $student_id)
{
	$query = "
	SELECT tbl_advisor.advisor_name 
	FROM tbl_student 
	INNER JOIN tbl_department 
	ON tbl_department.dept_id = tbl_student.student_dept_id 
	INNER JOIN tbl_advisor 
	ON tbl_advisor.advisor_dept_id = tbl_department.dept_id 
	WHERE tbl_student.student_id = '".$student_id."'
	";
	// $statement = $connect->prepare($query);
	// $statement->execute();
	// $result = $statement->fetchAll();
	$statement = mysqli_query($connect, $query);
    while($row = mysqli_fetch_assoc($statement)){
        return $row["advisor_name"];
    }
	// foreach($result as $row)
	// {
	// 	return $row["advisor_name"];
	// }
}

function Get_dept_name($connect, $dept_id)
{
	$query = "
	SELECT dept_name FROM tbl_department 
	WHERE dept_id = '".$dept_id."'
	";
	// $statement = $connect->prepare($query);
	// $statement->execute();
	// $result = $statement->fetchAll();
	$statement = mysqli_query($connect, $query);
    while($row = mysqli_fetch_assoc($statement)){
        return $row["dept_name"];
    }
	// foreach($result as $row)
	// {
	// 	return $row["dept_name"];
	// }
}

?>