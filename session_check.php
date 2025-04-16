<?php
    session_start();
    include("./settings/connect_datebase.php");

    if(isset($_SESSION['user']) && isset($_SESSION['user_token'])) {
        $user_id = $_SESSION['user'];
        $session_token = $_SESSION['user_token'];
        
        $stmt = $mysqli->prepare("SELECT session_token FROM user_sessions WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($db_token);
        if($stmt->fetch()){
            if($db_token !== $session_token) {
                session_destroy();
                header("Location: login.php?error=duplicate_session");
                exit;
            }
        } else {
            session_destroy();
            header("Location: login.php?error=duplicate_session");
            exit;
        }
        $stmt->close();
    }
?>
