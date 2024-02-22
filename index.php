<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Note Taking App</title>
</head>
<body>
<?php
session_start();

// array of usera credentials
$users = [
    'amr@gmail.com' => 'amr123',
    'mo@gmail.com' => 'mo1234',
    'joe@gmail.com' => 'joe123'

];

function sanitize_input($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

//validatiion
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}
function validate_password($password) {
    return strlen($password) >= 6; 
}

//authentication
function authenticate_user($email, $password, $users) {
    return isset($users[$email]) && $users[$email] === $password;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // login
    if (isset($_POST['login'])) {
        $email = sanitize_input($_POST['email']);
        $password = sanitize_input($_POST['password']);

        if (!validate_email($email) || !validate_password($password)) {
            echo "Invalid email or password format.";
        } else {
            if (authenticate_user($email, $password, $users)) {
                $_SESSION['user'] = $email;
                echo "Login successful!";
            } else {
                echo "Invalid email or password.";
            }
        }
    }

    // logout
    if (isset($_POST['logout'])) {
        unset($_SESSION['user']);
        echo "Logged out successfully!";
    }
}
?>

<div id="loginContainer" <?php if(isset($_SESSION['user'])) { echo 'style="display: none;"'; } ?>>
    <h2>Login</h2>
    <form method="post">
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br>
        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br>
        <input type="submit" name="login" value="Login">
    </form>
</div>

<div id="noteContainer" <?php if(!isset($_SESSION['user'])) { echo 'style="display: none;"'; } ?>>
    <h2>Note Taking Interface</h2>
    <form>
        <label for="note_content">Your Note:</label><br>
        <textarea id="note_content" rows="10" cols="50"></textarea><br>
        <input type="submit" value="Save Note">
    </form>
    <form method="post">
        <input type="submit" name="logout" value="Logout">
    </form>
</div>

<div id="savedNotes"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const noteContentInput = document.getElementById('note_content');


    document.querySelector('#noteContainer form').addEventListener('submit', function(event) {
        event.preventDefault(); 
        
        const noteContent = noteContentInput.value.trim();
        const userKey = '<?php echo isset($_SESSION['user']) ? $_SESSION['user'] : ''; ?>';
        const noteKey = userKey + '_notes';

        let savedNotes = localStorage.getItem(noteKey);
        savedNotes = savedNotes ? JSON.parse(savedNotes) : [];
        savedNotes.push(noteContent);
        localStorage.setItem(noteKey, JSON.stringify(savedNotes));
        alert('Note saved successfully');
        displaySavedNotes();
    });

//view notes
    function displaySavedNotes() {
        const userKey = '<?php echo isset($_SESSION['user']) ? $_SESSION['user'] : ''; ?>';
        const noteKey = userKey + '_notes';
        const savedNotes = localStorage.getItem(noteKey);
        const savedNotesDiv = document.getElementById('savedNotes');
        savedNotesDiv.innerHTML = '';

        if (savedNotes) {
            const notes = JSON.parse(savedNotes);
            savedNotesDiv.innerHTML = '<h3>Saved Notes:</h3>';
            notes.forEach(function(note, index) {
                savedNotesDiv.innerHTML += '<p>Note ' + (index + 1) + ': ' + note + '</p>';
            });
        } else {
            savedNotesDiv.innerHTML = '<p>No saved notes found.</p>';
        }
    }
    displaySavedNotes();
});
</script>

</body>
</html>