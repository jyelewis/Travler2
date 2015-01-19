<?php
$access = 'login';
require('SysData/init.php');
//die(var_dump($userTied));
//print_r($db->query("select * from Windows"));
//$db->query("UPDATE Windows SET hasChanged='1'");

/*$app = new travlr_application('test.jyelewis');
//$app->newWindow('window1', 'first class window', 'blank.html');
$form = new html_form();
$form -> addinput('open', 'submit', 'open');
$form -> addinput('close', 'submit', 'close');
$form -> addinput('title', 'submit', 'title');
$form -> addinput('url', 'submit', 'url');
$form -> addinput('fs', 'submit', 'fs');
if ($form->ispostback)
{
    if($form->postback->inputisset('open'))
    {
        echo 'open';
        $app->newWindow('window1', 'first class window', 'blank.html');
    }
    if($form->postback->inputisset('close'))
    {
        echo 'close';
        $window = $app->getWindow('window1');
        $window->close();
    }
    if($form->postback->inputisset('title'))
    {
        echo 'title';
        $window = $app->getWindow('window1');
        $window->setTitle(md5(rand()).md5(rand()));
    }
    if($form->postback->inputisset('url'))
    {
        echo 'url';
        $window = $app->getWindow('window1');
        if ($window->info['URL'] == 'blank.html')
        {
            $window->setURL('tied.php');
        } else {
            $window->setURL('blank.html');
        }
    }
    if($form->postback->inputisset('fs'))
    {
        echo 'fs';
        $window = $app->getWindow('window1');
        $window->fullscreen();
    }
}
echo $form;*/

//background images resize and insert

$db->writeQuery("DELETE FROM backgroundImages");
$db->writeQuery("INSERT INTO backgroundImages (
                    `userID`, `image`, `isDefault`
                ) VALUES (
                    ':1', ':2', '1'
                )", $userTied->userID, base64_encode(html_image::resized('images/backgroundImages/1.jpg', 800, 0)));
$db->writeQuery("INSERT INTO backgroundImages (
                    `userID`, `image`, `isDefault`
                ) VALUES (
                    ':1', ':2', '0'
                )", $userTied->userID, base64_encode(html_image::resized('images/backgroundImages/2.jpg', 800, 0)));
$db->writeQuery("INSERT INTO backgroundImages (
                    `userID`, `image`, `isDefault`
                ) VALUES (
                    ':1', ':2', '0'
                )", $userTied->userID, base64_encode(html_image::resized('images/backgroundImages/3.png', 800, 0)));
$db->writeQuery("INSERT INTO backgroundImages (
                    `userID`, `image`, `isDefault`
                ) VALUES (
                    ':1', ':2', '0'
                )", $userTied->userID, base64_encode(html_image::resized('images/backgroundImages/4.jpg', 800, 0)));

?>
