<?php

require('../config.php');

if (isset($_POST["org-assign-user-btn"])) {
    $username = $_POST['username'];
    $userRole = $_POST['userRole'];
    $orgid = $_POST['orgid'];
    $orgname = $_POST['orgname'];
    $assignCode = substr(md5(uniqid(mt_rand(), true)), 0, 8);

    $getUser = "SELECT * FROM users WHERE username = '$username'";
    $getUserResult = mysqli_query($conn, $getUser);

    if (mysqli_num_rows($getUserResult) > 0) {
        $getUserRow = mysqli_fetch_array($getUserResult);
        $getUserID = $getUserRow['id'];

        $getUserData = "SELECT * FROM org_members WHERE memberID=$getUserID AND orgID=$orgid";
        $getUserDataResult = mysqli_query($conn, $getUserData);

        if (mysqli_num_rows($getUserDataResult) < 1) {
            $stmt = $conn->prepare("INSERT INTO org_members (orgName, orgID, orgMember, memberID, orgRole, assignCode, confirmJoined) VALUES (?, ?, ?, ?, ?, ?, 0)");
            $stmt->bind_param("sisiss", $orgname, $orgid, $username, $getUserID, $userRole, $assignCode);

            if ($stmt->execute()) {
                header("Location: ../../components/assign/adduserorg.php?id=$orgid");
            } else {
                echo "An error has occurred adding user to org: " . mysqli_error($conn);
            }

            $stmt->close();
        } else {
            echo "User already in org!";
        }
    } else {
        echo "User does not exist!";
    }
}

?>
