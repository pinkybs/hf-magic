<?php

require_once "../client/360quan.php";

$qn = new Client_360quan("[api key]", "[api secret]");

$qn->requireFrame();
$uid = $qn->requireLogin();

$res = $qn->client->usersGetInfo($uid, "sex,birthday");
print_r($res);

?>

<br />
    Hello, <qn:name uid="<?php echo $uid ?>" useyou="false" />
<br />

    <qn:profile-pic uid="<?php echo $uid ?>" />
