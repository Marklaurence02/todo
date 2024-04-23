<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="colorbg">

<?php
// Establish database connection
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "todo";

$db = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Check connection
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if task ID is provided
if(isset($_GET['id'])) {
    $task_id = $_GET['id'];

    // Fetch task details from the database based on task ID
    $query = "SELECT * FROM tasks WHERE id = $task_id";
    $result = mysqli_query($db, $query);

    if(mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $task_name = $row['task'];
        $task_status = $row['status'];
        // Assuming you have more fields, fetch them similarly
    } else {
        // Task with the provided ID not found
        echo "Task not found.";
        exit();
    }
} else {
    // Task ID not provided
    echo "Task ID not provided.";
    exit();
}

// Check if form is submitted
if(isset($_POST['submit'])) {
    // Retrieve updated task details from the form
    $updated_task_name = $_POST['task_name'];
    $updated_task_status = $_POST['task_status'];
    // Assuming you have more fields, retrieve them similarly

    // Update the task in the database
    $update_query = "UPDATE tasks SET task = '$updated_task_name', status = '$updated_task_status' WHERE id = $task_id";
    $update_result = mysqli_query($db, $update_query);

    if($update_result) {
        // Task updated successfully
        echo "<span class='success-message'>Task updated successfully.";
        exit();
    } else {
        // Error updating task
        echo "Error updating task: " . mysqli_error($db);
        exit();
    }
}

// Check if delete file button is clicked
if(isset($_POST['delete_file'])) {
    // Update the database to set file name and file type to NULL
    $update_file_query = "UPDATE tasks SET file_name = NULL, file_type = NULL WHERE id = $task_id";
    $update_file_result = mysqli_query($db, $update_file_query);

    if($update_file_result) {
        // Data in file_name and file_type columns deleted successfully
        echo "<span class='success-message'>File data deleted successfully.</span>";
        
    } else {
        // Error updating file data in the database
        echo "Error updating file data in the database: " . mysqli_error($db);
    }
// Check if add file button is clicked
if(isset($_POST['add_file'])) {
    // Check if a file was uploaded successfully
    if(isset($_FILES['uploaded_file']) && $_FILES['uploaded_file']['error'] === UPLOAD_ERR_OK) {
        $file_name = $_FILES['uploaded_file']['name']; // Get the name of the uploaded file
        $file_tmp = $_FILES['uploaded_file']['tmp_name']; // Get the temporary location of the uploaded file

        // Move the uploaded file to a permanent location (uploads directory)
        $uploads_directory = 'uploads/';
        $target_path = $uploads_directory . basename($file_name);
        if(move_uploaded_file($file_tmp, $target_path)) {
            // File uploaded successfully, update the database
            $file_type = $_FILES['uploaded_file']['type']; // Get the MIME type of the uploaded file

            // Update the database to set file name and file type
            $update_file_query = "UPDATE tasks SET file_name = '$file_name', file_type = '$file_type' WHERE id = $task_id";
            $update_file_result = mysqli_query($db, $update_file_query);

            if($update_file_result) {
                // Data in file_name and file_type columns updated successfully
                echo "<span class='success-message'>File uploaded and added successfully.</span>";
            } else {
                // Error updating file data in the database
                echo "Error updating file data in the database: " . mysqli_error($db);
            }
        } else {
            // Error moving uploaded file
            echo "Error uploading file.";
        }
    } else {
        // No file uploaded or an error occurred while uploading
        echo "No file uploaded or an error occurred.";
    }

    // Exit script after processing file upload
    exit();
}
    // Exit script after database update
    exit();
}

?>

<script>
    function exitToIndex() {
        window.location.href = 'index.php'; // Redirect to index.php
        window.close(); // Close the window
    }
</script>


    <h1 class="text">Edit Task</h1>
    <form method="post" action="">
        <label for="task_name">Task Name:</label><br>
        <input type="text" id="task_name" name="task_name" value="<?php echo $task_name; ?>"><br>
<br>
        <label for="task_status">Task Status:</label><br>
        <select id="task_status" name="task_status">
            <option value="incomplete" <?php if($task_status == 'incomplete') echo 'selected'; ?>>Incomplete</option>
            <option value="pending" <?php if($task_status == 'pending') echo 'selected'; ?>>Pending</option>
            <option value="complete" <?php if($task_status == 'complete') echo 'selected'; ?>>Complete</option>
        </select><br>
        <!-- Assuming you have more fields, display them similarly -->
<br>
        <input type="submit" name="submit" value="Update Task">
    </form>

    <!-- Form to delete file -->
    <form method="post" action="">
        <input type="submit" name="delete_file" value="Delete File">
    </form>

    <!-- Form to add file -->
    <form2 method="post" action="">
    <input type="file" name="file" class="upload_input">
    </form2>
</body>
</html>
