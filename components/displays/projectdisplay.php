<?php

    require('../../backend/config.php');

    session_start();

    if($_SESSION['username'] != null) {
        $username = $_SESSION['username'];
    } else {
        header("Location: ../auth/login.php");
    }
    
    $projectid = $_GET['id'];
    $page = $_GET['page'];

    if($projectid == null) {
        echo "No Project ID Found";
    }

    if($page == null) {
        $page = 1;
    }

    $getProjectInfo = "SELECT * FROM projects WHERE id='$projectid'";
    $result = $conn->query($getProjectInfo);



    $limit = 6 * $page;
    $offset = $limit - 6;

    $getBugs = "SELECT * FROM bugs WHERE projectID='$projectid' LIMIT $offset, $limit";
    $getBugsRes = $conn->query($getBugs);

    #total bugs
    $getTotalBugs = "SELECT * FROM bugs WHERE projectID='$projectid'";
    $getTotalBugsRes = $conn->query($getTotalBugs);
    $totalBugs = $getTotalBugsRes->num_rows;
    $totalPages = ceil($totalBugs / 6);

    if($totalBugs > 0) {
        if($page > $totalPages) {
            header("Location: projectdisplay.php?id=$projectid&page=1");
        }
    
        $count = 0;
    }


    if(mysqli_num_rows($result) > 0) {
        while ($row1 = mysqli_fetch_array($result)) {
            $orgid = $row1['orgID'];
            $orgname = $row1['orgName'];

            $getBugMembers = "SELECT * FROM org_members WHERE orgID='$orgid' AND orgMember='$username'";
            $getBugMembersRes = $conn->query($getBugMembers);

            if(mysqli_num_rows($getBugMembersRes) < 1) {
                header("Location: ../root/organization.php");
            }

            if(mysqli_num_rows($getBugMembersRes) > 0) {
                while ($row = mysqli_fetch_array($getBugMembersRes)) {
                    $orgMember = $row['orgMember'];
    
?>

<!DOCTYPE html>
<html>
    
    <head>
        <title id="title"><?=htmlspecialchars($row1['projectName'])?></title>

        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width; initial-scale=1; maximum-scale=1; user-scalable=0">
        <meta name="author" content="Camsdono Studios">
        <meta name="description" content="A better place to keep track of your bugs and manage teams">

        <link rel="apple-touch-icon" sizes="180x180" href="../../images/favicon/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="../../images/favicon/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="../../images/favicon/favicon-16x16.png">
        <link rel="manifest" href="../../images/favicon/site.webmanifest">

        <link rel="stylesheet" href="../../styles/styles.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    </head>
    <body  class="blue">
        <section class="blue">
            <div class="topnav" id="myTopnav">
                <a href="../root/home.php">Home</a>
                <a href="../root/organization.php">Organizations</a>
                <a href="#">Tickets</a>
                <a href="../../backend/auth/logout.php">Logout</a>

                <a href="javascript:void(0);" class="icon" onclick="OpenCloseNav()">
                    <i class="fa fa-bars"></i>
                </a>
            </div>


            <ul class="breadcrumbs">
                <li class="breadcrumbs-item">
                    <a href="../root/organization.php?id=<?=$row1['orgID']?>" class="breadcrumbs-link">Your Organizations</a>
                </li>
                <li class="breadcrumbs-item">
                    <a href="./orgdisplay.php?id=<?=$row1['orgID'] ?>" class="breadcrumbs-link"><?=htmlspecialchars($row1['orgName'])?></a>
                </li>
            </ul>

            <div class="org-description">
                <p><?=htmlspecialchars($row1['projectDesc'])?></p>
            </div>

            <?php

            if(mysqli_num_rows($getBugsRes) > 0) {
                
                ?>
                <div class="org-row">
                    <a class="create-org" href="../creation/createbug.php?id=<?=$projectid?>">Add Bug</a>
                </div>
                <div class="bug-container">
            <?php
                while ($row1 = mysqli_fetch_array($getBugsRes)) {
                    $bugID = $row1['id'];
                    # only show 5 bugs at a time
                    if($count < 6) {
                        $count++;
                        ?>
                        <div class="bug-holder" onclick="window.location.href='./bugdisplay.php?bugID=<?=$bugID?>'">
                        <div class="bug-row">
                            <div class="bug-title">
                                <h4><?=htmlspecialchars($row1['bugName'])?></h4>
                            </div>
                        </div>
                        </div>
                        <?php
                    } 
                }

                if($totalPages > 1) {
                    ?>
                    <div class="pagination">
                        <?php
                        for($i = 1; $i <= $totalPages; $i++) {
                            ?>
                            <a href="?id=<?=$projectid?>&page=<?=$i?>"><?=$i?></a>
                            <?php
                        }
                        ?>
                    </div>
                    <?php
                }

                ?>
                </div>
                <?php
            } else {
                ?>
                    <h4 class="info-no-data">There Are No Bugs Currently In The Project Would You Like To <a class="link" href="../creation/createbug.php?id=<?=$projectid?>">Add</a> One!</h4>
                <?php
            }
            ?>
        </section>
    </body>
    
    <?php
                     
                }
                    } else {
                        header("Location: ../root/organization.php");
                    }
            }
        } else {
            header("Location: ../root/organization.php");
        }
    ?>
</html>
<script src="../../js/openCloseNavBar.js"></script>