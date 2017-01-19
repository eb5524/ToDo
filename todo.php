<!DOCTYPE html>
<html>
<body>
<h1>ToDo List</h1>
<?php
$servername = "localhost";
$username = "todo";
$password = "todopass123";
$dbname = "todo";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo $conn->connect_error;
}


$sql = "CREATE TABLE IF NOT EXISTS All_Tasks (task_id INT NOT NULL AUTO_INCREMENT, task_name VARCHAR(32), PRIMARY KEY(task_id))";

if (!($conn->query($sql))){
	echo $conn->error;
}

$sql = "CREATE TABLE IF NOT EXISTS Active_Tasks (task_id INT NOT NULL, task_description VARCHAR(255), PRIMARY KEY(task_id), FOREIGN KEY(task_id) REFERENCES All_Tasks(task_id))";

if (!($conn->query($sql))){
	echo $conn->error;
}

$sql = "CREATE TABLE IF NOT EXISTS Inactive_Tasks (task_id INT NOT NULL, complete_order INT NOT NULL AUTO_INCREMENT, PRIMARY KEY(complete_order), FOREIGN KEY(task_id) REFERENCES All_Tasks(task_id))";

if (!($conn->query($sql))){
	echo $conn->error;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if ($_POST["submit"] == "Create Task"){
		$name = $_POST["name"];
		$desc = $_POST["description"];
		$sql = "INSERT INTO All_Tasks (task_name) VALUES ('$name')";
		$taskid = -1;
		if (!($conn->query($sql))){
			echo $conn->error;
		} else {
			$taskid = $conn->insert_id;
		}
		$sql = "INSERT INTO Active_Tasks (task_id, task_description) VALUES ('$taskid', '$desc')";
		if (!($conn->query($sql))){
			echo $conn->error;
		}
	} else if ($_POST["submit"] == "Delete"){
		$id = $_POST["task_id"];
		$sql = "DELETE FROM Active_Tasks WHERE task_id='$id'";
		if (!($conn->query($sql))){
			echo $conn->error;
		}
		$sql = "INSERT INTO Inactive_Tasks (task_id) VALUES ('$id')";
		if (!($conn->query($sql))){
			echo $conn->error;
		}
	} else if ($_POST["submit"] == "View"){
		$id = $_POST["task_id"];
		$sql = "SELECT l.task_name, c.task_description FROM Active_Tasks AS c, All_Tasks AS l WHERE l.task_id = c.task_id AND c.task_id = '$id'";
		$result = $conn->query($sql)->fetch_assoc();
		if (!$result){
			$conn->error;
		} else {
			$desc = $result["task_description"];
			$name = $result["task_name"];
			echo "$name<br>$desc<br><br>";
		}
	} else if ($_POST["submit"] == "View Completed Tasks"){
		$sql = "SELECT l.task_name, i.complete_order FROM All_Tasks l, Inactive_Tasks i WHERE l.task_id = i.task_id ORDER BY complete_order ASC";
		$result = $conn->query($sql);
		if (!$result){
			echo $conn->error;
		} else {
			echo "<h2>Completed Tasks</h2>";
			if ($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {
					echo $row["task_name"] . "<br>";
				}
			} else {
				echo "No completed tasks<br>";
			}
			echo "<br>";
		}
	}
}

$sql = "SELECT l.task_id, l.task_name, c.task_description FROM Active_Tasks AS c, All_Tasks AS l WHERE l.task_id = c.task_id";

$result = $conn->query($sql);

if (!$result){
	echo $conn->error;
} else {
	echo "<table><tr><th>Task Name</th><th>Description</th><th>Delete</th></tr>";
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$id = $row["task_id"];
			echo "<tr><td>" . $row["task_name"] . "</td><td><form method='post' action=''><input type='hidden' name='task_id' value='$id'><input type='submit' name='submit' value='View'></form></td><td><form method='post' action=''><input type='hidden' name='task_id' value='$id'><input type='submit' name='submit' value='Delete'></form></td></tr>";
			
		}
	} else {
		echo "<tr><td>No Tasks</td></tr>";
	}
	echo "</table>";
}

$conn->close();
?>

<br>
<form method="post" action="">
Task Name:<br><input type="text" name="name" required><br>
Description:<br><textarea rows="4" cols="50" name="description"></textarea><br>
<input type="submit" name="submit" value="Create Task">
</form>
<br><br>
<form method="post" action="">
<input type="submit" name="submit" value="View Completed Tasks">
</form>
</body>
</html>
