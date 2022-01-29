<?php

//attendance_action.php

include('admin/db_connection.php');

session_start();


if(isset($_POST["action"]))
{
	if($_POST["action"] == "fetch")
	{
		$query = "
		SELECT * FROM tbl_attendance 
		INNER JOIN tbl_student 
		ON tbl_student.student_id = tbl_attendance.student_id 
		INNER JOIN tbl_department 
		ON tbl_department.dept_id = tbl_student.student_dept_id 
		WHERE tbl_attendance.advisor_id = '".$_SESSION["advisor_id"]."' AND tbl_attendance.attendance_date = '".$_POST["cur_date"]."' AND (
		";

		if(isset($_POST["search"]["value"]))
		{
			$query .= '
			tbl_student.student_name LIKE "%'.$_POST["search"]["value"].'%" 
			OR tbl_student.student_id LIKE "%'.$_POST["search"]["value"].'%" 
            OR tbl_department.dept_year LIKE "%'.$_POST["search"]["value"].'%" 
            OR tbl_department.dept_name LIKE "%'.$_POST["search"]["value"].'%"
			OR tbl_attendance.attendance_status LIKE "%'.$_POST["search"]["value"].'%" 
			OR tbl_attendance.attendance_date LIKE "%'.$_POST["search"]["value"].'%") 
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
			ORDER BY tbl_attendance.attendance_date DESC 
			';
		}

		if($_POST["length"] != -1)
		{
			$query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}

		$statement = mysqli_query($connect, $query);
		$data = array();
		$filtered_rows = mysqli_num_rows($statement);
		while($row = mysqli_fetch_assoc($statement))
		{
			$sub_array = array();
			$status = '';
			if($row["attendance_status"] == "Present")
			{
				$status = '<label class="badge badge-success">Present</label>';
			}

			if($row["attendance_status"] == "Absent")
			{
				$status = '<label class="badge badge-danger">Absent</label>';
			}

			$sub_array[] = $row["student_name"];
			$sub_array[] = $row["student_id"];
            $sub_array[] = $row["dept_year"];
			$sub_array[] = $row["dept_name"];
			$sub_array[] = $status;
			$data[] = $sub_array;
		}

		$output = array(
			'draw'				=>	intval($_POST["draw"]),
			"recordsTotal"		=> 	$filtered_rows,
			"recordsFiltered"	=>	get_total_records($connect, 'tbl_attendance'),
			"data"				=>	$data
		);

		echo json_encode($output);
	}

	if($_POST["action"] == "Add")
	{
		$attendance_date = '';
		$error_attendance_date = '';
		$error = 0;
		if(empty($_POST["attendance_date"]))
		{
			$error_attendance_date = 'Attendance Date is required';
			$error++;
		}
		else
		{
			$attendance_date = $_POST["attendance_date"];
		}

		if($error > 0)
		{
			$output = array(
				'error'							=>	true,
				'error_attendance_date'			=>	$error_attendance_date
			);
		}
		else
		{
			$student_id = $_POST["student_id"];
			$query = '
			SELECT attendance_date FROM tbl_attendance 
			WHERE advisor_id = "'.$_SESSION["advisor_id"].'" 
			AND attendance_date = "'.$attendance_date.'"
			';
			$statement = mysqli_query($connect, $query);
			if(mysqli_num_rows($statement) > 0)
			{
				$output = array(
					'error'					=>	true,
					'error_attendance_date'	=>	'Attendance Data Already Exists on this date'
				);
			}
			else
			{
				for($count = 0; $count < count($student_id); $count++)
				{
						$_student_id			=	$student_id[$count];
						$_attendance_status	=	$_POST["attendance_status".$student_id[$count].""];
						$_attendance_date		=	$attendance_date;
						$_advisor_id			=	$_SESSION["advisor_id"];

					$query = "
					INSERT INTO tbl_attendance 
					(student_id, attendance_status, attendance_date, advisor_id) 
					VALUES ('$_student_id', '$_attendance_status', '$_attendance_date', '$_advisor_id')
					";
					$statement = mysqli_query($connect, $query);
				}
				$output = array(
					'success'		=>	'Data Added Successfully',
				);
			}
		}
		echo json_encode($output);
	}

	if($_POST["action"] == "index_fetch")
	{
		$query = "
		SELECT * FROM tbl_attendance 
		INNER JOIN tbl_student 
		ON tbl_student.student_id = tbl_attendance.student_id 
		INNER JOIN tbl_department 
		ON tbl_department.dept_id = tbl_student.student_dept_id 
		WHERE tbl_attendance.advisor_id = '".$_SESSION["advisor_id"]."' AND (
		";
		if(isset($_POST["search"]["value"]))
		{
			$query .= '
			tbl_student.student_name LIKE "%'.$_POST["search"]["value"].'%" 
			OR tbl_student.student_id LIKE "%'.$_POST["search"]["value"].'%" 
			OR tbl_department.dept_name LIKE "%'.$_POST["search"]["value"].'%" )
			';
		}
		$query .= 'GROUP BY tbl_student.student_id ';
		if(isset($_POST["order"]))
		{
			$query .= '
			ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' 
			';
		}
		else
		{
			$query .= '
			ORDER BY tbl_student.student_id ASC 
			';
		}

		if($_POST["length"] != -1)
		{
			$query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}

		$statement = mysqli_query($connect, $query);
		$data = array();
		$filtered_rows = mysqli_num_rows($statement);
		while($row = mysqli_fetch_assoc($statement))
		{
            if($row["student_dept_id"] == $_SESSION["dept_id"]){
			$sub_array = array();
			$sub_array[] = $row["student_name"];
			$sub_array[] = $row["student_id"];
            $sub_array[] = $row["dept_year"];
			$sub_array[] = $row["dept_name"];
			$sub_array[] = get_attendance_percentage($connect, $row["student_id"]);
			$sub_array[] = '<button type="button" name="report_button" id="'.$row["student_id"].'" class="btn btn-info btn-sm report_button">Report</button>';
			$data[] = $sub_array;
		}
        }
		$output = array(
			'draw'					=>	intval($_POST["draw"]),
			"recordsTotal"		=> 	$filtered_rows,
			"recordsFiltered"	=>	get_total_records($connect, 'tbl_student'),
			"data"				=>	$data
		);
		echo json_encode($output);
	}
}


?>