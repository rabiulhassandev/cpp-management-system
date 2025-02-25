<?php

	if(!isset($current_user)){die('Unauthorized Error');}
	$current_user_id = $current_user['id'];

	if (isset($_POST['details_submit'])=='submit') {
		$instituteName = htmlentities(addslashes($_POST['instituteName']));
		$instituteEmail = htmlentities(addslashes($_POST['instituteEmail']));
		$institutePhone = htmlentities(addslashes($_POST['institutePhone']));
		$instituteAddress = htmlentities(addslashes($_POST['instituteAddress']));
		$currentInstituteLogo = htmlentities(addslashes($_POST['currentInstituteLogo']));

		if(!$instituteName == '' && !$instituteEmail == '' && !$institutePhone == ''){
			
			if (!empty($_FILES['logo']['name'])) {
				$my_file = $_FILES["logo"]['name'];
		        $file_TmpName = $_FILES['logo']['tmp_name'];

				$extension = pathinfo($my_file, PATHINFO_EXTENSION);
				$valid_extension = array("JPG", "jpg", "PNG", "png", "gif", "jpeg");

				if (in_array($extension, $valid_extension)) {
					$file_Name = rand() . "." . $extension;
				
					move_uploaded_file($file_TmpName, "images/$file_Name");

					$unlink_img = "images/".$currentInstituteLogo;
					if (file_exists($unlink_img)) {
						unlink($unlink_img);
					}

					$currentInstituteLogo = $file_Name;
					
				}else{
					echo "<script>alert('Invalid file'); location.href='profile.php';</script>";
				}
			}
			
			$updateSql = "UPDATE `admin` SET `name` = '$instituteName', `email` = '$instituteEmail', `phone` = '$institutePhone', `address` = '$instituteAddress', `image` = '$currentInstituteLogo' WHERE `id` = '$current_user_id'";

			// echo $updateSql;die();

			$runUpdateQuery = mysqli_query($conn, $updateSql);
			if ($runUpdateQuery) {
				echo "<script>alert('Information Updated Successful'); location.href='profile.php';</script>";
			}else{
				echo "<script>alert('Sorry! something wrong.'); location.href='profile.php';</script>";
			}
		}else{
			echo "<script>alert('All field are required.'); location.href='profile.php';</script>";
		}
	}else if (isset($_POST['pass_update'])=='submit') {

		$username = htmlentities(addslashes($_REQUEST['username']));
        $current_pass = htmlentities(addslashes(md5(trim($_POST['old_pass']))));
        $new_pass = htmlentities(addslashes(md5(trim($_POST['new_pass']))));
        $confirm_pass = htmlentities(addslashes(md5(trim($_POST['confirm_pass']))));

		if (!$current_pass == '' && !$new_pass == '' && !$confirm_pass == '') {

			$current_user_id = $current_user['id'];
			$select_pwd ="SELECT * FROM `admin` WHERE id = '$current_user_id'";
	        $run_query = mysqli_query($conn,$select_pwd);
	        while($getoldpwd = mysqli_fetch_array($run_query)){
	            $oldpwd = $getoldpwd['password'];
	        }

			$auth = md5(trim($username.$confirm_pass));

	        if ($oldpwd===$current_pass) {
	            if ($confirm_pass===$new_pass) {
	                $updatePwdQuery = "UPDATE admin SET username='$username', password='$confirm_pass', auth='$auth' WHERE id = '$current_user_id'";
					// echo $updatePwdQuery; die();

	                $runPassUpdateQuery = mysqli_query($conn,$updatePwdQuery);
	                if ($runPassUpdateQuery) {
	                    echo "<script>alert('Update Successful'); location.href='index.php';</script>";
	                }else{
	                	echo "<script>alert('something wrong...'); location.href='profile.php';</script>";
	                }
	            }else{
	                echo "<script>alert('New password and confirm password is not match'); location.href='profile.php';</script>";
	            }
	        }else{
	        	echo "<script>alert('old password is invalid'); location.href='profile.php';</script>";
	        }

		}else{
			echo "<script>alert('All field are required'); location.href='profile.php';</script>";
		}
	}

?>
<div class="row">
	<div class="col-md-4 mx-auto pb-0 pt-5 mt-5">
		<div class="card mb-2" id="profile_card">
			<img src="<?php echo "images/". $current_user['image'] ?? null; ?>" onerror="this.src='assets/images/logo.png'" class="my-3" style="max-height: 100px; max-width: 100px; margin: auto; border-radius: 10px;">
			<h4 class="text-center"><?php echo $current_user['name']; ?></h4>
			<h6 class="text-center"><?php echo $current_user['address']; ?></h6>
			<p class="text-center m-0"><?php echo $current_user['email']; ?></p>
			<p class="text-center"><?php echo $current_user['phone']; ?></p>

			<div class="row pb-3">
				<div class="col-md-10 text-center mx-auto">
					<button class="btn btn-primary btn-block" data-toggle="modal" data-target="#instituteinfoModal">Update Shop Information</button>
				</div>
			</div>

		</div>
	</div>
	<div class="col-md-12 text-center">
		<p><a href="#" data-toggle="modal" data-target="#pass_modal">Update Login Details ?</a></p>
	</div>

	<div class="modal fade" id="instituteinfoModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title" id="exampleModalLabel">Update Shop Information</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<form method="POST" enctype="multipart/form-data">
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<label><b>Shop Name :</b></label>
						<input type="text" placeholder="Shop name" name="instituteName" value="<?php echo $current_user['name']; ?>" class="form-control" required>
					</div>
					<div class="col-md-12 pt-2">
						<label><b>Shop Email :</b></label>
						<input type="email" placeholder="Shop Email" name="instituteEmail" value="<?php echo $current_user['email']; ?>" class="form-control" required>
					</div>
					<div class="col-md-12 pt-2">
						<label><b>Shop Phone :</b></label>
						<input type="text" placeholder="Shop Phone" name="institutePhone" value="<?php echo $current_user['phone']; ?>" class="form-control" required>
					</div>
					<div class="col-md-12 pt-2">
						<label><b>Address :</b></label>
						<input type="text" placeholder="Enter Address" name="instituteAddress" value="<?php echo $current_user['address']; ?>" class="form-control" required>
					</div>
					<div class="col-md-12 pt-2">
						<label><b>Shop Logo :</b></label><br>
						<img src="<?php echo "images/". $current_user['image'] ?? null; ?>" onerror="this.src='assets/images/logo.png'" style="max-height: 120px; max-width: 120px;">
						<br>
						<input type="hidden" name="currentInstituteLogo" value="<?php echo $current_user['image']; ?>" required readonly>
						<input type="file" name="logo">
					</div>

				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="submit" name="details_submit" class="btn btn-primary">Save Changes</button>
			</div>
		</form>
		</div>
	</div>
	</div>

	<div class="modal fade" id="pass_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog " role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Update Login Info</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form method="post" action="">
					<div class="modal-body">
						<div class="row">
							<div class="col-12">
								<div class="form-group">
									<label for="phone_no" class="col-form-label">Current Username:</label>
									<input type="text" class="form-control" name="username" placeholder="Enter username" value="<?php echo $current_user['username']; ?>" required>
								</div>
								<div class="form-group">
									<label for="phone_no" class="col-form-label">Current Passoword:</label>
									<input type="password" class="form-control" name="old_pass" value="" placeholder="Current Passoword" required>
								</div>
								<div class="form-group">
									<label for="phone_no" class="col-form-label">New Passoword :</label>
									<input type="password" class="form-control" name="new_pass" placeholder="New Passoword" required>
								</div>
								<div class="form-group">
									<label for="phone_no" class="col-form-label">Confirm Passoword :</label>
									<input type="password" class="form-control" name="confirm_pass" placeholder="Confirm Passoword" required>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<div class="row justify-content-center w-100">
							<div class="col-12 text-center">
								<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
								<button type="submit" class="btn btn-primary" name="pass_update">Save Change</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>