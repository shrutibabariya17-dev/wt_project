// auth.js - Authentication & Session Management for Student Hub

const DEFAULT_STUDENT = {
    name: "Shruti Babariya",
    rollno: "25aiml001",
    email: "shruti@gmail.com",
    course: "B.Tech AIML",
    department: "CSPIT, CHARUSAT",
    semester: "Semester 3",
    gpa: "8.00 SGPA",
    role: "student"
};

function getSession() {
    try {
        return JSON.parse(sessionStorage.getItem("studenthub_session") || "null");
    } catch (e) {
        return null;
    }
}

function setSession(user) {
    sessionStorage.setItem("studenthub_session", JSON.stringify(user));
}

function clearSession() {
    sessionStorage.removeItem("studenthub_session");
}

function logout() {
    if (confirm("Are you sure you want to log out of Student Hub?")) {
        clearSession();
        window.location.href = "login.html";
    }
}

// Page protection guard: redirects to login.html if not authenticated
function protectPage() {
    const session = getSession();
    if (!session) {
        alert("Access Restricted: Please sign in first to access this section of Student Hub.");
        window.location.replace("login.html");
        return false;
    }
    return true;
}

// Admin protection guard: redirects if not an administrator
function protectAdminPage() {
    const session = getSession();
    if (!session || session.role !== "admin") {
        alert("Access Restricted: Administrator privileges are required to view this page.");
        window.location.replace("login.html");
        return false;
    }
    return true;
}

// Dynamically render the navigation bar according to authentication state
function renderAuthNav() {
    const session = getSession();
    const nav = document.querySelector("nav");
    if (!nav) return;

    const cur = window.location.pathname.split("/").pop().toLowerCase();

    if (!session) {
        // Logged Out State: Only show authentication & public info links
        nav.innerHTML = `
            <a href="login.html" class="${cur === 'login.html' ? 'active' : ''}">Sign In</a>
            <a href="registration.html" class="${cur === 'registration.html' ? 'active' : ''}">Register</a>
            <a href="about.html" class="${cur === 'about.html' ? 'active' : ''}">About Us</a>
            <a href="contact.html" class="${cur === 'contact.html' ? 'active' : ''}">Contact Us</a>
        `;
    } else {
        // Logged In State: Show full portal navigation with Logout
        const isAdmin = session.role === "admin";
        const displayName = session.name || session.rollno || "Student";

        let html = `
            <a href="index.html" class="${cur === '' || cur === 'index.html' ? 'active' : ''}">Home</a>
            <a href="campuslife.html" class="${cur === 'campuslife.html' ? 'active' : ''}">Campus Life</a>
            <a href="COURSEENROLLMENT.html" class="${cur === 'courseenrollment.html' ? 'active' : ''}">Course Enrollment</a>
            <a href="events.html" class="${cur === 'events.html' ? 'active' : ''}">Events</a>
            <a href="faq.html" class="${cur === 'faq.html' ? 'active' : ''}">FAQ</a>
            <a href="dashboard.html" class="${cur === 'dashboard.html' ? 'active' : ''}">Dashboard</a>
            <a href="about.html" class="${cur === 'about.html' ? 'active' : ''}">About Us</a>
            <a href="contact.html" class="${cur === 'contact.html' ? 'active' : ''}">Contact Us</a>
        `;

        if (isAdmin) {
            html += `<a href="manager.html" class="${cur === 'manager.html' ? 'active' : ''}">Administrative</a>`;
        }

        html += `<a href="javascript:void(0)" onclick="logout()" style="background-color: #E53E3E; margin-left: 6px;">🚪 Logout (${displayName})</a>`;

        nav.innerHTML = html;
    }
}

// Auto-run on DOM ready
document.addEventListener("DOMContentLoaded", renderAuthNav);
