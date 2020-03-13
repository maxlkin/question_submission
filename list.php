<?php
require_once "../config.php";
include_once "./api.php";
include_once "./index.php";

use \Tsugi\Core\LTIX;
$LAUNCH = LTIX::requireData();
$p = $CFG->dbprefix;
$link_id = $LAUNCH->link->id;

$rows = API::refresh();

if ($rows) {
    foreach ($rows as $row) {
        $id = $row['id'];
        $question = $row['question_text'];
        $date = $row['date_created'];
        $anon = $row['anonymous'];
        $user = $row["quser_id"];
        $username = "Anonymous";
        if ($anon != 1) {
            $sql = "SELECT * FROM {$p}lti_user WHERE user_id = $user";
            $result = $PDOX->allRowsDie($sql);
            $username = $result[0]['displayname'];
        }          

        if ($user == $USER->id) {
            $actions = "<form action='index.php' method='post'>
                                        <input type='hidden' value=$id name='remove_id'>
                                        <button type='submit' class='secondary-content btn red'><i class='material-icons'>clear</i></button>
                                    </form>";
        } else {
            $actions = "";
        }


        $sql = "SELECT COUNT(*) FROM {$p}qs_vote WHERE question_id = $id";
        $result = $PDOX->rowDie($sql);
        $count = $result['COUNT(*)'];

        $upvotes = $row['upvotes'];

        echo "
                    <li class='collection-item avatar'>
                        <form action='index.php' method='post'>
                            <input type='hidden' value=$USER->id name='upvoter_id'>
                            <input type='hidden' value=$id name='question_id'>
                            <button type='submit' class='circle red button-color'>
                                <p>+
                                <br>
                                $upvotes</p>
                            </button>
                        </form>
                        <span class='title'>$username</span>
                        <p class='question'>$question</p>
                        $actions
                    </li>
                    ";
    }
} else {
    echo "<li class='collection-item'>No questions yet!</li>";
}