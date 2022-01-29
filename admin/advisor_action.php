<?php

//advisor_action.php

include('db_connection.php');

session_start();

if(isset($_POST["action"]))
{
	if($_POST["action"] == "fetch")
	{
		$query = "
		SELECT * FROM tbl_advisor 
		INNER JOIN tbl_department 
		ON tbl_department.dept_id = tbl_advisor.advisor_dept_id 
		";
		if(isset($_POST["search"]["value"]))
		{
			$query .= '
			WHERE tbl_advisor.advisor_name LIKE "%'.$_POST["search"]["value"].'%" 
			OR tbl_advisor.advisor_email LIKE "%'.$_POST["search"]["value"].'%" 
			OR tbl_department.dept_name LIKE "%'.$_POST["search"]["value"].'%" 
			';
		}
		if(isset($_POST["order"]))
		{
			$query .= '
			ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].'
			';
		}
		else
		{
			$query .= '
			ORDER BY tbl_advisor.advisor_id DESC 
			';
		}
		if($_POST["length"] != -1)
		{
			$query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}

		// $statement = $connect->prepare($query);
		// $statement->execute();
		// $result = $statement->fetchAll();
		$statement = mysqli_query($connect, $query);
		$data = array();
		// $filtered_rows = $statement->rowCount();
		$filtered_rows = mysqli_num_rows($statement);
		// foreach($result as $row)
		// {
		// 	$sub_array = array();
		// 	$sub_array[] = '<img src="advisor_image/'.$row["advisor_image"].'" class="img-thumbnail" width="75" />';
		// 	$sub_array[] = $row["advisor_name"];
		// 	$sub_array[] = $row["advisor_email"];
        //     $sub_array[] = $row["dept_name"];
		// 	$sub_array[] = $row["dept_year"];
		// 	$sub_array[] = '<button type="button" name="view_advisor" class="btn btn-info btn-sm view_advisor" id="'.$row["advisor_id"].'">View</button>&nbsp;&nbsp;<button type="button" name="edit_advisor" class="btn btn-primary btn-sm edit_advisor" id="'.$row["advisor_id"].'">Edit</button>&nbsp;&nbsp;<button type="button" name="delete_advisor" class="btn btn-danger btn-sm delete_advisor" id="'.$row["advisor_id"].'">Delete</button>';
		// 	$data[] = $sub_array;
		// }
		while($row = mysqli_fetch_assoc($statement)){
			$sub_array = array();
			$sub_array[] = '<img src="advisor_image/'.$row["advisor_image"].'" class="img-thumbnail" width="75" />';
			$sub_array[] = $row["advisor_name"];
			$sub_array[] = $row["advisor_email"];
            $sub_array[] = $row["dept_name"];
			$sub_array[] = $row["dept_year"];
			$sub_array[] = '<button type="button" name="view_advisor" class="btn btn-info btn-sm view_advisor" id="'.$row["advisor_id"].'">View</button>&nbsp;&nbsp;<button type="button" name="edit_advisor" class="btn btn-primary btn-sm edit_advisor" id="'.$row["advisor_id"].'">Edit</button>&nbsp;&nbsp;<button type="button" name="delete_advisor" class="btn btn-danger btn-sm delete_advisor" id="'.$row["advisor_id"].'">Delete</button>';
			$data[] = $sub_array;
		}
		$output = array(
			"draw"				=>	intval($_POST["draw"]),
			"recordsTotal"		=> 	$filtered_rows,
			"recordsFiltered"	=>	get_total_records($connect, 'tbl_advisor'),
			"data"				=>	$data
		);
		echo json_encode($output);
	}

	if($_POST["action"] == 'Add' || $_POST["action"] == "Edit")
	{
		$advisor_name = '';
		$advisor_id = '';
		$advisor_email = '';
		$advisor_password = '';
		$advisor_dept_id = '';
		$advisor_image = '';
		$error_advisor_name = '';
		$error_advisor_id = '';
		$error_advisor_email = '';
		$error_advisor_password = '';
		$error_advisor_dept_id = '';
		$error_advisor_image = '';
		$error = 0;

		$advisor_image = $_POST["hidden_advisor_image"];
		if($_FILES["advisor_image"]["name"] != '')
		{
			$file_name = $_FILES["advisor_image"]["name"];
			$tmp_name = $_FILES["advisor_image"]["tmp_name"];
			$extension_array = explode(".", $file_name);
			$extension = strtolower($extension_array[1]);
			$allowed_extension = array('jpg','png');
			if(!in_array($extension, $allowed_extension))
			{
				$error_advisor_image = 'Invalid Image Format';
				$error++;
			}
			else
			{
				$advisor_image = uniqid() . '.' . $extension;
				$upload_path = 'advisor_image/' . $advisor_image;
				move_uploaded_file($tmp_name, $upload_path);
			}
		}
		else
		{
			if($advisor_image == '')
			{
				$error_advisor_image = 'Image is required';
				$error++;
			}
		}
		if(empty($_POST["advisor_name"]))
		{
			$error_advisor_name = 'Advisor Name is required';
			$error++;
		}
		else
		{
			$advisor_name = $_POST["advisor_name"];
		}
		if(empty($_POST["advisor_id"]))
		{
			$error_advisor_id = 'Advisor ID is required';
			$error++;
		}
		else
		{
			$advisor_id = $_POST["advisor_id"];
		}
		if($_POST["action"] == "Add")
		{
			if(empty($_POST["advisor_email"]))
			{
				$error_advisor_email = 'Email Address is required';
				$error++;
			}
			else
			{
				if(!filter_var($_POST["advisor_email"], FILTER_VALIDATE_EMAIL))
				{
					$error_advisor_email = 'Invalid email format';
					$error++;
				}
				else
				{
					$advisor_email = $_POST["advisor_email"];
				}
			}
			if(empty($_POST["advisor_password"]))
			{
				$error_advisor_password = "Password is required";
				$error++;
			}
			else
			{
				$advisor_password = $_POST["advisor_password"];
			}
		}
		if(empty($_POST["advisor_dept_id"]))
		{
			$error_advisor_dept_id = "Department and Year is required";
			$error++;
		}
		else
		{
			$advisor_dept_id = $_POST["advisor_dept_id"];
		}
		if($error > 0)
		{
			$output = array(
				'error'							=>	true,
				'error_advisor_name'			=>	$error_advisor_name,
				'error_advisor_id'			=>	$error_advisor_id,
				'error_advisor_email'			=>	$error_advisor_email,
				'error_advisor_password'		=>	$error_advisor_password,
				'error_advisor_dept_id'		=>	$error_advisor_dept_id,
				'error_advisor_image'			=>	$error_advisor_image
			);
		}
		else
		{
			if($_POST["action"] == 'Add')
			{
					$_advisor_name			=	$advisor_name;
					$_advisor_id		=	$advisor_id;
					$_advisor_email		=	$advisor_email;
					$_advisor_password		=	password_hash($advisor_password, PASSWORD_DEFAULT);
					$_advisor_image		=	$advisor_image;
					$_advisor_dept_id		=	$advisor_dept_id;
				$query = "
				INSERT INTO tbl_advisor 
				(advisor_name, advisor_id, advisor_email, advisor_password, advisor_image, advisor_dept_id) 
				SELECT * FROM (SELECT '$_advisor_name', '$_advisor_id', '$_advisor_email', '$_advisor_password', '$_advisor_image', '$_advisor_dept_id') as temp 
				WHERE NOT EXISTS (
					SELECT advisor_email FROM tbl_advisor WHERE advisor_email = '$_advisor_email'
				) LIMIT 1
				";
				// $statement = $connect->prepare($query);
				// if($statement->execute($data))
				// {
				$statement = mysqli_query($connect, $query);
				if($statement)
				{
					$output = array(
						'success'		=>	'Data Added Successfully',
					);
				}
				else
				{
					$output = array(
						'error'					=>	true,
						'error_advisor_email'	=>	'Email Already Exists'
					);
				}
				// }
			}
			if($_POST["action"] == "Edit")
			{
					$_advisor_name		=	$advisor_name;
					$_advisor_image	=	$advisor_image;
					$_advisor_dept_id	=	$advisor_dept_id;
					$_advisor_id		=	$_POST["advisor_id"];
				$query = "
				UPDATE tbl_advisor 
				SET advisor_name = '$_advisor_name', 
				advisor_image = '$_advisor_image',  
				advisor_dept_id = '$_advisor_dept_id'
				WHERE advisor_id = '$_advisor_id'
				";
				$statement = mysqli_query($connect, $query);
				if($statement)
				{
					$output = array(
						'success'		=>	'Data Edited Successfully',
					);
				}
			}
		}
		echo json_encode($output);
	}



	if($_POST["action"] == "single_fetch")
	{
		// $query = "
		// SELECT * FROM tbl_advisor 
		// INNER JOIN tbl_department 
		// ON tbl_department.dept_id = tbl_advisor.advisor_dept_id 
		// WHERE tbl_advisor.advisor_id = '".$_POST["advisor_id"]."'";
		// // $statement = $connect->prepare($query);
		// $statement = mysqli_query($connect, $query);
		// if($statement->execute())
		// {
		// 	$result = $statement->fetchAll();
		// 	$output = '
		// 	<div class="row">
		// 	';
		// 	foreach($result as $row)
		// 	{
		// 		$output .= '
		// 		<div class="col-md-3">
		// 			<img src="advisor_image/'.$row["advisor_image"].'" class="img-thumbnail" />
		// 		</div>
		// 		<div class="col-md-9">
		// 			<table class="table">
		// 				<tr>
		// 					<th>Name</th>
		// 					<td>'.$row["advisor_name"].'</td>
		// 				</tr>
		// 				<tr>
		// 					<th>Advisor ID</th>
		// 					<td>'.$row["advisor_id"].'</td>
		// 				</tr>
		// 				<tr>
		// 					<th>Email Address</th>
		// 					<td>'.$row["advisor_email"].'</td>
		// 				</tr>
		// 				<tr>
		// 					<th>Department</th>
		// 					<td>'.$row["dept_name"].'</td>
		// 				</tr>
		// 				<tr>
		// 					<th>Year</th>
		// 					<td>'.$row["dept_year"].'</td>
		// 				</tr>
		// 			</table>
		// 		</div>
		// 		';
		// 	}
		// 	$output .= '</div>';
		// 	echo $output;
		$query = "
		SELECT * FROM tbl_advisor 
		INNER JOIN tbl_department 
		ON tbl_department.dept_id = tbl_advisor.advisor_dept_id 
		WHERE tbl_advisor.advisor_id = '".$_POST['advisor_id']."'";
		// $statement = $connect->prepare($query);
		$statement = mysqli_query($connect, $query);
		$output = '
		<div class="row">
		';
		while($row = mysqli_fetch_assoc($statement)){
			$output .= '
				<div class="col-md-3">
					<img src="advisor_image/'.$row["advisor_image"].'" class="img-thumbnail" />
				</div>
				<div class="col-md-9">
					<table class="table">
						<tr>
							<th>Name</th>
							<td>'.$row["advisor_name"].'</td>
						</tr>
						<tr>
							<th>Advisor ID</th>
							<td>'.$row["advisor_id"].'</td>
						</tr>
						<tr>
							<th>Email Address</th>
							<td>'.$row["advisor_email"].'</td>
						</tr>
						<tr>
							<th>Department</th>
							<td>'.$row["dept_name"].'</td>
						</tr>
						<tr>
							<th>Year</th>
							<td>'.$row["dept_year"].'</td>
						</tr>
					</table>
				</div>
				';
		}
		// foreach($result as $row)
		// 	{
				
		// 	}
		$output .= '</div>';
		echo $output;
		}

	if($_POST["action"] == "edit_fetch")
	{
		$query = "
		SELECT * FROM tbl_advisor WHERE advisor_id = '".$_POST["advisor_id"]."'
		";
		// $statement = $connect->prepare($query);
		$statement = mysqli_query($connect, $query);
		// $result = $statement->fetchAll();
		// foreach($result as $row)
		// {
		// 	$output["advisor_name"] = $row["advisor_name"];
		// 	$output["advisor_id"] = $row["advisor_id"];
		// 	$output["advisor_image"] = $row["advisor_image"];
		// 	$output["advisor_email"] = $row["advisor_email"];
		// 	$output["advisor_dept_id"] = $row["advisor_dept_id"];
		// }
		while($row = mysqli_fetch_assoc($statement)){
			$output["advisor_name"] = $row["advisor_name"];
			$output["advisor_id"] = $row["advisor_id"];
			$output["advisor_image"] = $row["advisor_image"];
			$output["advisor_email"] = $row["advisor_email"];
			$output["advisor_dept_id"] = $row["advisor_dept_id"];
		}
		echo json_encode($output);
	}

	if($_POST["action"] == "delete")
	{
		// $query = "
		// DELETE FROM tbl_advisor 
		// WHERE advisor_id = '".$_POST["advisor_id"]."'
		// ";
		// $statement = mysqli_query($query);
		// if($statement)
		// {
		// 	echo 'Data Deleted Successfully';
		// }
		$query = "
		DELETE tbl_advisor, tbl_student, tbl_attendance 
		FROM tbl_advisor INNER JOIN tbl_student INNER JOIN tbl_attendance 
		WHERE tbl_student.student_dept_id = tbl_advisor.advisor_dept_id AND tbl_attendance.student_id = tbl_student.student_id AND tbl_advisor.advisor_id='".$_POST["advisor_id"]."'
		";
		$statement = mysqli_query($connect, $query);
		if($statement == true)
		{
			$query2 = "
			DELETE FROM tbl_advisor
			WHERE advisor_id='".$_POST["advisor_id"]."'
			";
			$statement2 = mysqli_query($connect, $query2);
			if($statement2){
				echo 'Data Deleted Successfully';
			}
		}
		else if($statement){
			echo 'Data Deleted Successfully';
		}
	}
	
}

?>