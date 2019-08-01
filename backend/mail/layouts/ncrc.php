<h1>An innvitation  to join the project.</h1>

<p>
    <?= $detail;?>
</p>
<?php
$reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
$textUrl = '';
if (preg_match($reg_exUrl, $url)) {
    $textUrl = $url;
} else {
    $url = \cpn\chanpan\classes\CNServerConfig::getProtocol();
    $domain = \cpn\chanpan\classes\CNServerConfig::getDomainName();
    $url = $url.$domain;
}
?>
