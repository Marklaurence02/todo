<?php
// Database connection details
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "todo";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize errors variable
$errors = "";

// Connect to database
$db = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Insert a task if submit button is clicked
if (isset($_POST['submit'])) {
    $task = $_POST["task"];
    $task_status = $_POST["task_status"]; // Get task status
    
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
            move_uploaded_file($file_tmp, "uploads/" . $file_name);
            header('location: index.php');
        } else {
            $errors = "Sorry, there was an error storing information in the database: " . mysqli_error($db);
        }
    }
}

if (isset($_GET['del_task'])) {
    $id = $_GET['del_task'];
    mysqli_query($db, "DELETE FROM tasks WHERE id=$id");
    header('location: index.php');
}
// Check if task status is provided in the URL
if(isset($_GET['task_status'])) {
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
        $sql .= " WHERE status = 'completed'";
    } elseif ($task_status == 'incomplete') {
        $sql .= " WHERE status = 'incomplete'";
    } elseif ($task_status == 'pending') {
        $sql .= " WHERE status = 'pending'";
    }
}
// Check if task ID to mark as complete is provided
if(isset($_GET['mark_complete'])) {
    $task_id = $_GET['mark_complete'];
    
    // Update task status to "complete"
    $update_query = "UPDATE tasks SET status = 'complete' WHERE id = $task_id";
    $update_result = mysqli_query($db, $update_query);
    
    if($update_result) {
        // Task status updated successfully
        header("Location: index.php"); // Redirect to refresh the page
        exit();
    } else {
        // Error updating task status
        echo "Error updating task status: " . mysqli_error($db);
    }
}
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

<form method="post" action="index.php" class="input_form" enctype="multipart/form-data">
    <?php if (isset($errors)) { ?>
        <p><?php echo $errors; ?></p>
    <?php } ?>
    <input type="text" name="task" class="task_input">
    <input type="file" name="file" class="upload_input">
    <select name="task_status" class="task_select">
        <option value="pending">Pending Tasks</option> <!-- Changed value to 'pending' -->
    </select>
    <button type="submit" name="submit" id="add_btn" class="add_btn">Add Task</button>
</form>

<table>
    <thead>
    <tr>
        <th>N</th>
        <th>Tasks</th>
        <th>File</th>
        <th>Edit</th>
        <th>Status</th>
        <th style="width: 60px;">Action</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $i = 1;
    while ($row = mysqli_fetch_array($tasks)) {
        ?>
        <tr>
            <td><?php echo $i; ?></td>
            <td class="task"><?php echo $row['task']; ?></td>
            <td>
                <?php if (!empty($row['file_name'])) { ?>
                    <a href="uploads/<?php echo $row['file_name']; ?>" class="btn_primary" download>Download</a>
                <?php } else {
                    echo "No file uploaded";
                } ?>
            </td>
            <td>
                <button onclick="editTask(<?php echo $row['id']; ?>)">Edit</button>
            </td>
            <td class="status"><?php echo ucfirst($row['status']); ?></td>
            <td class="delete">
                <a href="index.php?del_task=<?php echo $row['id']; ?>">x</a>
            </td>
        </tr>
        <?php
        $i++;
    }
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
<script>
function editTask(taskId) {
    // Open a pop-up window for editing the task
    window.open("edit_task.php?id=" + taskId, "Edit Task", "width=400,height=400");
}
</script>




<form method="get" action="index.php" class="input_form">
    <select name="task_status" class="task_select">
        <option value="all">All Tasks</option>
        <option value="complete">Completed Tasks</option>
        <option value="incomplete">Incomplete Tasks</option>
        <option value="pending">Pending Tasks</option>
    </select>
    <button type="submit" name="select" class="select_btn">Select</button>
</form>

<table>
    <thead>
    <tr>
        <th>N</th>
        <th>Tasks</th>
        <th>File</th>
        <th>Status</th>
        <th style="width: 60px;">Submit</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $i = 1;
    $task_status = isset($_GET['task_status']) ? $_GET['task_status'] : 'all';
    $query = "SELECT * FROM tasks WHERE status = '$task_status' OR status = ''";
    if ($task_status === 'all') {
        $query = "SELECT * FROM tasks";
    }
    $tasks_result = mysqli_query($db, $query);
    while ($row = mysqli_fetch_array($tasks_result)) {
        ?>
        <tr>
            <td><?php echo $i; ?></td>
            <td class="task"><?php echo $row['task']; ?></td>
            <td>
                <?php if (!empty($row['file_name'])) { ?>
                    <a href="uploads/<?php echo $row['file_name']; ?>" class="btn_primary" download>Download</a>
                <?php } else {
                    echo "No file uploaded";
                } ?>
            </td>
            <td><?php echo ucfirst($row['status']); ?></td>
            <td class="Complete">
                <?php
                if ($row['status'] === 'complete') {
                    echo "Done";
                } elseif ($row['status'] === 'incomplete') {
                    echo "Late";
                } elseif ($row['status'] === 'pending') {
                    echo "<button type='submit' name='submit' class='submit_btn'>Submit</button>";
                }
                ?>
            </td>
        </tr>
        <?php
        $i++;
    }
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
