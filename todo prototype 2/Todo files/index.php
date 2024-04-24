<?php
// Database connection details
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "todo";

// Create a new MySQLi connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check for connection error
if ($conn->connect_error) {
    die("Connection failed: ". $conn->connect_error);
}

// Initialize errors variable
$errors = "";

// Connect to database (not needed, already connected using MySQLi)
$db = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Insert a task if submit button is clicked
if (isset($_POST['submit'])) {
    // Get task and task status from form submission
    $task = $_POST["task"];
    $task_status = $_POST["task_status"];
    
    // File upload
    $file_name = $_FILES['file']['name'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $file_type = $_FILES['file']['type'];

    // Check if task is empty
    if (empty($task)) {
        $errors = "You must fill in the task";
    } else {
        // Insert the task into the tasks table with status
        $sql = "INSERT INTO tasks (task, file_name, file_type, status) VALUES ('$task', '$file_name', '$file_type', '$task_status')";

        if (mysqli_query($db, $sql)) {
            // Move uploaded file to "Downloads" directory
            move_uploaded_file($file_tmp, "Downloads/". $file_name);
            header('location: index.php');
        } else {
            $errors = "Sorry, there was an error storing information in the database: ". mysqli_error($db);
        }
    }
}

// Delete a task if delete button is clicked
if (isset($_GET['del_task'])) {
    $id = $_GET['del_task'];
    mysqli_query($db, "DELETE FROM tasks WHERE id=$id");
    header('location: index.php');
}

// Check if task status is provided in the URL
if (isset($_GET['task_status'])) {
    $task_status = $_GET['task_status'];
    
    // Construct SQL query based on selected task status
    if ($task_status == 'all') {
        $sql = "SELECT * FROM tasks";
    } else {
        $sql = "SELECT * FROM tasks WHERE status = '$task_status'";
    }
    
    // Execute the query
    $tasks_result = mysqli_query($db, $sql);
} else {
    // If task status is not provided, fetch all tasks
    $tasks_result = mysqli_query($db, "SELECT * FROM tasks");
}

// Select all tasks from database
$sql = "SELECT * FROM tasks";
if (isset($_GET['task_statuss'])) {
    $task_status = $_GET['task_status'];
    if ($task_status == 'complete') {
        $sql.= " WHERE status = 'completed'";
    } elseif ($task_status == 'incomplete') {
        $sql.= " WHERE status = 'incomplete'";
    } elseif ($task_status == 'pending') {
        $sql.= " WHERE status = 'pending'";
    }
}

// Check if task ID to mark as complete is provided
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['mark_complete'])) {
    $task_id = $_POST['mark_complete'];
    
    // Update task status to "complete"
    $update_query = "UPDATE tasks SET status = 'complete' WHERE id =?";
    
    // Prepare and execute the update query
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("i", $task_id); // 'i' indicates integer type
    $update_result = $stmt->execute();
    
    if ($update_result) {
        // Task status updated successfully
        header("Location: index.php"); // Redirect to refresh the page
        exit();
    } else {
        // Error updating task status
        echo "Error updating task status: ". $stmt->error;
    }
}

// Fetch all tasks from database
$tasks = mysqli_query($db, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>ToDo List Application PHP and MySQL</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<div class="heading">
    <h2 style="font-style: 'Hervetica';">ToDo List Function System </h2>
</div>

<!-- Form to add a new task -->
<form method="post" action="index.php" class="input_form" enctype="multipart/form-data">
    <!-- Display error message if there is one -->
    <?php if (isset($errors)) { ?>
        <p><?php echo $errors; ?></p>
    <?php } ?>
    <!-- Input field for task name and file upload -->
    <input type="text" name="task" class="task_input" placeholder="Your default text here">
    <input type="file" name="file" class="upload_input">
    <!-- Select field for task status -->
    <select name="task_status" class="task_select">
        <option value="pending">Pending Tasks</option> <!-- Changed value to 'pending' -->
    </select>
    <!-- Submit button to add the task -->
    <button type="submit" name="submit" id="add_btn" class="add_btn">Add Task</button>
</form>

<!-- Table to display all tasks -->
<table>
    <thead>
    <tr>
        <th>N</th> <!-- Task number -->
        <th>Tasks</th> <!-- Task name -->
        <th>File</th> <!-- Task file -->
        <th>Edit</th> <!-- Edit button -->
        <th>Grading</th> <!-- Grading button -->
        <th>Status</th> <!-- Task status -->
        <th style="width: 60px;">Action</th> <!-- Delete button -->
    </tr>
    </thead>
    <tbody>
    <?php
    // Initialize task number variable
    $i = 1;
    // Loop through all tasks and display them in the table
    while ($row = mysqli_fetch_array($tasks)) {
        ?>
        <tr>
            <td><?php echo $i; ?></td> <!-- Display task number -->
            <td class="task"><?php echo $row['task']; ?></td> <!-- Display task name -->
            <td>
                <?php if (!empty($row['file_name'])) { ?>
                    <!-- Display download button if there is a file -->
                    <a href="uploads/<?php echo $row['file_name']; ?>" class="btn_primary" download>Download</a>
                <?php } else {
                    echo "No file uploaded";
                } ?>
            </td>
            <td>
                <!-- Edit button to open a pop-up window for editing the task -->
                <button onclick="editTask(<?php echo $row['id']; ?>)">Edit</button>
            </td>
            <td>Grades</td> <!-- Grading button -->
            <td class="status"><?php echo ucfirst($row['status']); ?></td> <!-- Display task status -->
            <td class="delete">
                <!-- Delete button to delete the task -->
                <a href="index.php?del_task=<?php echo $row['id']; ?>">x</a>
            </td>
        </tr>
        <?php
        $i++; // Increment task number
    }
    // Display message if there are no tasks
    if (mysqli_num_rows($tasks) == 0) {
        ?>
        <tr>
            <td colspan="6">No tasks found.</td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>

<!-- JavaScript function to open a pop-up window for editing the task -->
<script>
function editTask(taskId) {
    window.open("edit_task.php?id=" + taskId, "Edit Task", "width=400,height=400");
}
</script>

<!-- Form to filter tasks by status -->
<form method="get" action="index.php" class="input_form">
    <!-- Select field for task status -->
    <select name="task_status" class="task_select">
        <option value="all" <?php if(isset($_GET['task_status']) && $_GET['task_status'] == 'all') echo 'selected'; ?>>All Tasks</option>
        <option value="complete" <?php if(isset($_GET['task_status']) && $_GET['task_status'] == 'complete') echo 'selected'; ?>>Completed Tasks</option>
        <option value="incomplete" <?php if(isset($_GET['task_status']) && $_GET['task_status'] == 'incomplete') echo 'selected'; ?>>Incomplete Tasks</option>
        <option value="pending" <?php if(isset($_GET['task_status']) && $_GET['task_status'] == 'pending') echo 'selected'; ?>>Pending Tasks</option>
    </select>
    <!-- Submit button to filter tasks -->
    <button type="submit" name="select" class="select_btn">Select</button>
</form>

<!-- Table to display filtered tasks -->
<table>
    <thead>
    <tr>
        <th>N</th> <!-- Task number -->
        <th>Tasks</th> <!-- Task name -->
        <th>File</th> <!-- Task file -->
        <th>Status</th> <!-- Task status -->
        <th style="width: 60px;">Submit</th> <!-- Submit button for completed tasks -->
    </tr>
    </thead>
    <tbody>
    <?php
    // Initialize task number variable
    $i = 1;
    // Get the selected task status from the URL
    $task_status = isset($_GET['task_status']) ? $_GET['task_status'] : 'all';
    // Construct the SQL query based on the selected task status
    $query = "SELECT * FROM tasks WHERE status = '$task_status' OR status = ''";
    if ($task_status === 'all') {
        $query = "SELECT * FROM tasks";
    }
    // Execute the SQL query and store the result
    $tasks_result = mysqli_query($db, $query);
    // Loop through all tasks and display them in the table
    while ($row = mysqli_fetch_array($tasks_result)) {
        ?>
        <tr>
            <td><?php echo $i; ?></td> <!-- Display task number -->
            <td class="task"><?php echo $row['task']; ?></td> <!-- Display task name -->
            <td>
                <?php if (!empty($row['file_name'])) { ?>
                    <!-- Display download button if there is a file -->
                    <a href="uploads/<?php echo $row['file_name']; ?>" class="btn_primary" download>Download</a>
                <?php } else {
                    echo "No file uploaded";
                } ?>
            </td>
            <td><?php echo ucfirst($row['status']); ?></td> <!-- Display task status -->
            <td class="complete">
                <!-- Submit button for completed tasks -->
                <?php
                if ($row['status'] === 'complete') {
                    echo "<span class='complete'>Done</span>";
                } elseif ($row['status'] === 'incomplete') {
                    echo "<span class='incomplete'>Late</span>";
                } elseif ($row['status'] === 'pending') {
                    echo "<form method='post'><input type='hidden' name='mark_complete' value='" . $row['id'] . "'><button type='done' class='done_btn'>Done</button></form>";
                }
                ?>
            </td>
        </tr>
        <?php
        $i++; // Increment task number
    }
    // Display message if there are no tasks
    if (mysqli_num_rows($tasks_result) == 0) {
        ?>
        <tr>
            <td colspan="5">No tasks found.</td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>

</body>
</html>