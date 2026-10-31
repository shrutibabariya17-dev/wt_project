-- Student Hub Database SQL Script
-- Database: studenthub
-- Author: Shruti Babariya & Anshika Badala
-- CSPIT, CHARUSAT - Web Technology Project

CREATE DATABASE IF NOT EXISTS `studenthub` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `studenthub`;

-- --------------------------------------------------------
-- Table structure for table `students`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `students` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `roll_no` VARCHAR(50) NOT NULL UNIQUE,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `course` VARCHAR(100) NOT NULL DEFAULT 'B.Tech AIML',
    `semester` INT DEFAULT 3,
    `department` VARCHAR(100) DEFAULT 'Computer Engineering',
    `mobile` VARCHAR(20) DEFAULT '',
    `gender` VARCHAR(20) DEFAULT 'Female',
    `address` TEXT DEFAULT NULL,
    `sgpa` DECIMAL(3,2) DEFAULT 8.00,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `admins`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `admins` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `role` VARCHAR(50) DEFAULT 'Manager',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `courses`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `courses` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `course_code` VARCHAR(20) NOT NULL UNIQUE,
    `course_name` VARCHAR(100) NOT NULL,
    `faculty` VARCHAR(100) NOT NULL,
    `credits` INT DEFAULT 4,
    `description` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `enrollments`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `enrollments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `student_id` INT NOT NULL,
    `course_id` INT NOT NULL,
    `semester` INT DEFAULT 3,
    `enrollment_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `status` VARCHAR(20) DEFAULT 'Enrolled',
    FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `events`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `events` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `category` VARCHAR(50) NOT NULL,
    `date` VARCHAR(50) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `contacts`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `contacts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `first_name` VARCHAR(50) NOT NULL,
    `last_name` VARCHAR(50) NOT NULL,
    `contact_no` VARCHAR(20) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `message` TEXT,
    `submitted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================================
-- Seed Initial Data
-- ========================================================

-- Insert Default Admin (username: admin, password: 123)
INSERT INTO `admins` (`username`, `password`, `name`, `role`) VALUES
('admin', '123', 'Administrator', 'System Manager')
ON DUPLICATE KEY UPDATE `username`=`username`;

-- Insert Students
INSERT INTO `students` (`roll_no`, `name`, `email`, `password`, `course`, `semester`, `department`, `mobile`, `gender`, `address`, `sgpa`) VALUES
('25aiml001', 'Shruti Babariya', 'shruti@gmail.com', '123456', 'B.Tech AIML', 3, 'Computer Engineering', '6424633581', 'Female', 'Anand, Gujarat', 8.00),
('25aiml002', 'Anshika Badala', 'anshikabadala@gmail.com', '123456', 'B.Tech AIML', 3, 'Computer Engineering', '9924633581', 'Female', 'Changa, Gujarat', 8.50),
('25aiml003', 'Disha Ghadiya', 'disha@gmail.com', '123456', 'B.Tech AIML', 3, 'Computer Engineering', '9824633581', 'Female', 'Nadiad, Gujarat', 8.20),
('25aiml004', 'Krisha Ghodasar', 'Krisha@gmail.com', '123456', 'B.Tech AIML', 3, 'Computer Engineering', '9924633581', 'Female', 'Ahmedabad, Gujarat', 8.10)
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- Insert Courses
INSERT INTO `courses` (`course_code`, `course_name`, `faculty`, `credits`, `description`) VALUES
('CS301', 'Java Programming', 'Jalpesh Vasa', 4, 'Core Object-Oriented Programming and Applications'),
('CS302', 'Web Development', 'Mukti Patel', 4, 'Frontend and Backend technologies: HTML, CSS, JS, PHP & MySQL'),
('CS303', 'Python Programming', 'Prajyot Tugaonkar', 4, 'Python Fundamentals, Data Science, and Machine Learning concepts'),
('CS304', 'Database Management Systems', 'Nirav Bhatt', 4, 'Relational Database Design, SQL Query Optimization, Normalization')
ON DUPLICATE KEY UPDATE `course_name`=VALUES(`course_name`);

-- Insert Enrollments
INSERT INTO `enrollments` (`student_id`, `course_id`, `semester`, `status`) VALUES
(1, 1, 3, 'Enrolled'),
(1, 2, 3, 'Enrolled'),
(1, 3, 3, 'Enrolled'),
(2, 1, 3, 'Enrolled'),
(2, 2, 3, 'Enrolled'),
(3, 2, 3, 'Enrolled'),
(3, 3, 3, 'Enrolled'),
(4, 1, 3, 'Enrolled'),
(4, 4, 3, 'Enrolled')
ON DUPLICATE KEY UPDATE `status`=VALUES(`status`);

-- Insert Events
INSERT INTO `events` (`name`, `category`, `date`, `description`) VALUES
('AI Workshop', 'Workshop', '20-08-2026', 'Hands-on training on Artificial Intelligence models'),
('Hackathon', 'Competition', '22-08-2026', '24-hour inter-college coding competition'),
('Flutter Workshop', 'Workshop', '25-08-2026', 'Cross-platform mobile application development'),
('Coding Contest', 'Competition', '28-08-2026', 'Speed programming and algorithmic problem solving'),
('Web Seminar', 'Seminar', '01-09-2026', 'Modern full-stack web architectures and security'),
('Python Workshop', 'Workshop', '03-09-2026', 'Python programming for data analytics'),
('Tech Talk', 'Seminar', '05-09-2026', 'Industry trends in AI and Cloud Computing'),
('Java Contest', 'Competition', '08-09-2026', 'Java problem solving championship'),
('Cyber Security Workshop', 'Workshop', '10-09-2026', 'Network security and ethical hacking foundations'),
('AI Seminar', 'Seminar', '12-09-2026', 'Deep dive into Generative AI and Large Language Models'),
('Code Hackathon', 'Competition', '15-09-2026', 'Project building hackathon for innovators'),
('Cloud Workshop', 'Workshop', '18-09-2026', 'Deploying scalable apps on AWS and Google Cloud'),
('Data Science Seminar', 'Seminar', '20-09-2026', 'Big Data pipelines and Machine Learning in production'),
('Web Design Contest', 'Competition', '22-09-2026', 'UI/UX and creative web page styling challenge'),
('JavaScript Workshop', 'Workshop', '25-09-2026', 'Modern ES6+, Asynchronous JavaScript and APIs')
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- Insert Contacts
INSERT INTO `contacts` (`first_name`, `last_name`, `contact_no`, `email`, `message`) VALUES
('Anshika', 'Badala', '9924633581', 'anshikabadala@gmail.com', 'Welcome to Student Hub support team.'),
('Shruti', 'Babariya', '6424633581', 'shruti@gmail.com', 'Owner & Lead Coordinator for Student Hub project.'),
('Disha', 'Ghadiya', '9824633581', 'disha@gmail.com', 'Student Hub Campus Life Coordinator.'),
('Krisha', 'Ghodasar', '9924633581', 'Krisha@gmail.com', 'Course Enrollment & Academic Representative.')
ON DUPLICATE KEY UPDATE `first_name`=VALUES(`first_name`);

