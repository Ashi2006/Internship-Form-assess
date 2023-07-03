<?php
require_once 'vendor/autoload.php'; 

$email = 'your_gmail_account@gmail.com';
$client_id = 'your_client_id';
$client_secret = 'your_client_secret';
$refresh_token = 'your_refresh_token';

$client = new Google_Client();
$client->setClientId($client_id);
$client->setClientSecret($client_secret);
$client->setAccessType('offline');
$client->setAccessToken($refresh_token);

if ($client->isAccessTokenExpired()) {
    $client->fetchAccessTokenWithRefreshToken();
    file_put_contents('token.json', json_encode($client->getAccessToken()));
}

$service = new Google_Service_Gmail($client);

$to = 'debashikunsh@gmail.com';
$subject = 'New Health Report Uploaded';
$message = 'A new health report has been uploaded.';

$email = new Google_Service_Gmail_Message();
$email->setRaw(rtrim(strtr(base64_encode($message), '+/', '-_'), '='));


$attachment = new Google_Service_Gmail_MessagePart();
$attachment->setMimeType('application/pdf');
$attachment->setFilename('health_report.pdf');
$attachment->setBody(file_get_contents($_FILES['health_report']['tmp_name']));
$email->setAttachments([$attachment]);

$service->users_messages->send('me', $email);

header("Location: success.php");
exit();
?>
