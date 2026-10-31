<?php
// manager.php - Database Manager & Admin Portal for Student Hub
// Database: studenthub (MySQL)

$conn = new mysqli('localhost', 'root', '', 'studenthub');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 1. Handle Registration from registration.html
if (isset($_GET['action']) && $_GET['action'] == 'register') {
    $name     = $_POST['name'] ?? '';
    $rollno   = $_POST['rollno'] ?? '';
    $email    = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $course   = $_POST['course'] ?? 'B.Tech AIML';

    if (!empty($name) && !empty($rollno)) {
        $stmt = $conn->prepare("INSERT INTO students (roll_no, name, email, password, course) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $rollno, $name, $email, $password, $course);
        if ($stmt->execute()) {
            echo "<script>alert('Registration successful! Please login with your ID and password.'); window.location.href='login.html';</script>";
            exit;
        } else {
            echo "<script>alert('Student ID or Email already exists in database.'); window.location.href='registration.html';</script>";
            exit;
        }
    }
}

// 2. Handle Login from login.html
if (isset($_GET['action']) && $_GET['action'] == 'login') {
    $uname = $_POST['uname'] ?? '';
    $pword = $_POST['pword'] ?? '';

    // Admin login
    if ($uname == 'admin' && $pword == '123') {
        echo "<script>alert('Valid Login: Admin Portal'); window.location.href='manager.php';</script>";
        exit;
    }

    // Student login in database
    $stmt = $conn->prepare("SELECT * FROM students WHERE (roll_no=? OR email=?) AND password=?");
    $stmt->bind_param("sss", $uname, $uname, $pword);
    $stmt->execute();
    $check = $stmt->get_result();
    if ($check && $check->num_rows > 0) {
        echo "<script>alert('Valid Login! Welcome to your Dashboard.'); window.location.href='dashboard.html';</script>";
        exit;
    } else {
        echo "<script>alert('Invalid Username or Password'); window.location.href='login.html';</script>";
        exit;
    }
}

// 3. Handle Course Enrollment from COURSEENROLLMENT.html
if (isset($_GET['action']) && $_GET['action'] == 'enroll') {
    $rollno    = $_POST['rollno'] ?? '';
    $course_id = intval($_POST['course_id'] ?? 1);

    $stmt = $conn->prepare("SELECT id FROM students WHERE roll_no=?");
    $stmt->bind_param("s", $rollno);
    $stmt->execute();
    $sRes = $stmt->get_result();
    if ($sRes && $sRes->num_rows > 0) {
        $sId = $sRes->fetch_assoc()['id'];
        $insert = $conn->prepare("INSERT INTO enrollments (student_id, course_id, status) VALUES (?, ?, 'Enrolled')");
        $insert->bind_param("ii", $sId, $course_id);
        $insert->execute();
        echo "<script>alert('Enrollment successful for Student ID: $rollno'); window.location.href='COURSEENROLLMENT.html';</script>";
        exit;
    } else {
        echo "<script>alert('Student ID ($rollno) not found! Please register first.'); window.location.href='COURSEENROLLMENT.html';</script>";
        exit;
    }
}

// 4. Handle Delete Student from Manager
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id = intval($_GET['id']);
    $conn->query("DELETE FROM students WHERE id=$id");
    header("Location: manager.php");
    exit;
}

// 5. Handle Add Student from Manager
if (isset($_POST['add_student'])) {
    $name     = $_POST['name'] ?? '';
    $rollno   = $_POST['rollno'] ?? '';
    $email    = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '123456';
    $course   = $_POST['course'] ?? 'B.Tech AIML';

    if (!empty($name) && !empty($rollno)) {
        $stmt = $conn->prepare("INSERT INTO students (roll_no, name, email, password, course) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $rollno, $name, $email, $password, $course);
        $stmt->execute();
    }
    header("Location: manager.php");
    exit;
}

// Fetch all students
$students = $conn->query("SELECT * FROM students ORDER BY id ASC");

// Fetch recent enrollments
$enrollments = $conn->query("
    SELECT e.id, s.roll_no, s.name as student_name, c.course_name, c.faculty, e.status, e.enrollment_date 
    FROM enrollments e 
    JOIN students s ON e.student_id = s.id 
    JOIN courses c ON e.course_id = c.id 
    ORDER BY e.id DESC LIMIT 10
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub - Administrative Database Manager</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .split-layout {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 25px;
            margin-top: 25px;
        }
        @media (max-width: 800px) {
            .split-layout {
                grid-template-columns: 1fr;
            }
        }
        .add-box {
            background-color: #F8F7FD;
            border: 1px solid #E2D9F3;
            border-radius: 8px;
            padding: 20px;
        }
        .badge-student {
            background-color: #5D3FD3;
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 12px;
        }
    </style>
</head>
<body>

    <div class="site-header">
        <h1>Student Hub</h1>
        <p>Administrative Database Control & Student Registry</p>
    </div>

    <nav>
        <a href="index.html">Home</a>
        <a href="campuslife.html">Campus Life</a>
        <a href="COURSEENROLLMENT.html">Course Enrollment</a>
        <a href="events.html">Events</a>
        <a href="faq.html">FAQ</a>
        <a href="dashboard.html">Dashboard</a>
        <a href="about.html">About Us</a>
        <a href="contact.html">Contact Us</a>
        <a href="login.html">Login</a>
        <a href="registration.html">Register</a>
        <a href="manager.php" class="active">Administrative</a>
    </nav>

    <div class="container">
        <h2>Registered Students in Database</h2>
        <p>The table below is live data queried directly from the MySQL database (<code>studenthub.students</code>):</p>

        <div class="split-layout">
            <div>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Roll No</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Course</th>
                        <th>Action</th>
                    </tr>
                    <?php while ($row = $students->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><span class="badge-student"><?= htmlspecialchars($row['roll_no']) ?></span></td>
                        <td><strong><?= htmlspecialchars($row['name']) ?></strong></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['course']) ?></td>
                        <td>
                            <a href="manager.php?action=delete&id=<?= $row['id'] ?>" 
                               onclick="return confirm('Delete student <?= htmlspecialchars($row['name']) ?>?');" 
                               style="color: #E53E3E; font-weight: bold; text-decoration: none;">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </table>

                <h3 style="margin-top: 35px; color: #5D3FD3;">Recent Course Enrollments</h3>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Student</th>
                        <th>Course</th>
                        <th>Faculty</th>
                        <th>Status</th>
                    </tr>
                    <?php while ($erow = $enrollments->fetch_assoc()): ?>
                    <tr>
                        <td><?= $erow['id'] ?></td>
                        <td><?= htmlspecialchars($erow['student_name']) ?> (<?= htmlspecialchars($erow['roll_no']) ?>)</td>
                        <td><strong><?= htmlspecialchars($erow['course_name']) ?></strong></td>
                        <td><?= htmlspecialchars($erow['faculty']) ?></td>
                        <td><span style="background-color:#C6F6D5; color:#22543D; padding:2px 8px; border-radius:10px; font-size:12px; font-weight:bold;"><?= $erow['status'] ?></span></td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            </div>

            <div class="add-box">
                <h3 style="color: #5D3FD3; margin-top: 0;">Add New Student</h3>
                <p style="font-size: 13px; color: #666; margin-bottom: 15px;">Manually insert a new student record into the MySQL database:</p>

                <form method="POST" action="manager.php">
                    <input type="hidden" name="add_student" value="1">

                    <label for="mname">Full Name:</label>
                    <input type="text" id="mname" name="name" placeholder="e.g. Shruti Babariya" required>

                    <label for="mroll">Roll No / ID:</label>
                    <input type="text" id="mroll" name="rollno" placeholder="e.g. 25aiml001" required>

                    <label for="memail">Email Address:</label>
                    <input type="email" id="memail" name="email" placeholder="e.g. shruti@gmail.com" required>

                    <label for="mpass">Password:</label>
                    <input type="password" id="mpass" name="password" value="123456">

                    <label for="mcourse">Course / Degree:</label>
                    <input type="text" id="mcourse" name="course" value="B.Tech AIML">

                    <input type="submit" value="Add Student to Database">
                </form>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 Student Hub | Database Management Console</p>
    </footer>

</body>
</html>
