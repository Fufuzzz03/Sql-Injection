<?php
include 'config.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Secure Admin Portal | CTF Challenge</title>
    <style>
        body { font-family: Arial; background: #0a0a0a; color: #00ff00; margin: 40px; }
        .container { max-width: 800px; margin: auto; background: #111; padding: 20px; border: 1px solid #00ff00; }
        input, button { padding: 10px; margin: 5px; }
        input { width: 250px; background: #222; color: #0f0; border: 1px solid #0f0; }
        button { background: #008800; color: white; border: none; cursor: pointer; }
        .flag { background: black; color: lime; padding: 15px; margin: 20px 0; border: 2px dashed red; font-family: monospace; }
        .error { color: #ff5555; }
        .success { color: #55ff55; }
        pre { background: #000; padding: 10px; overflow: auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Secure Admin Portal</h1>
        <p><em>Only administrators can view the flag.</em></p>
        
        <form method="POST">
            <h3>Login</h3>
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit">Login</button>
        </form>
        
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];
            
            // VULNERABLE SQL QUERY - NO PREPARED STATEMENTS
            $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
            
            echo "<div style='background:#1a1a1a;padding:10px;margin-top:20px;'>";
            echo "<strong>Executed Query:</strong><br><pre>$query</pre>";
            echo "</div>";
            
            $result = $conn->query($query);
            
            if ($result && $result->num_rows > 0) {
                $user = $result->fetch_assoc();
                
                echo "<h3 class='success'>✅ Welcome, " . htmlspecialchars($user['username']) . "!</h3>";
                echo "<p>Role: " . htmlspecialchars($user['role']) . "</p>";
                
                if ($user['role'] === 'admin') {
                    // Get flag from database
                    $flag_query = "SELECT flag FROM flags WHERE user_id = " . $user['id'];
                    $flag_result = $conn->query($flag_query);
                    $flag_row = $flag_result->fetch_assoc();
                    
                    echo "<h2>🎌 ADMIN ACCESS GRANTED</h2>";
                    echo "<div class='flag'>FLAG: " . $flag_row['flag'] . "</div>";
                    echo "<p><em>Submit this flag in CTF platform!</em></p>";
                } else {
                    echo "<p class='error'>❌ Regular users cannot view flags.</p>";
                }
            } else {
                echo "<p class='error'>❌ Invalid credentials.</p>";
            }
        }
        ?>
        
        <hr>
        <h3>💡 Challenge Hint:</h3>
        <p>The login form is vulnerable to SQL Injection. Your goal is to login as <strong>admin</strong> without knowing the password.</p>
        
   
    </div>
</body>
</html>