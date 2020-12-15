<?php
require_once 'Google/Client.php';
require_once 'Google/Service/Drive.php';

$client = new Google_Client();
// Get your credentials from the console
$client->setClientId('780761034559-5a69jrkteo36b85t6bn4ijr0n404t2te.apps.googleusercontent.com');
$client->setClientSecret('t25qxcALXV_SnzqUKqwUiDJ_');
$client->setRedirectUri('https://arslan.blogbaaz.com/googledrivesdk');
$client->setScopes(array('https://www.googleapis.com/auth/drive.file'));

session_start();

if (isset($_GET['code']) || (isset($_SESSION['access_token']) && $_SESSION['access_token'])) {
    if (isset($_GET['code'])) {
        $client->authenticate($_GET['code']);
        $_SESSION['access_token'] = $client->getAccessToken();
    } else
        $client->setAccessToken($_SESSION['access_token']);

    $service = new Google_Service_Drive($client);

    //Insert a file
    $file = new Google_Service_Drive_DriveFile();
    $file->setName(uniqid().'.jpg');
    $file->setDescription('A test document');
    $file->setMimeType('image/jpeg');

    $data = file_get_contents('a.jpg');

    $createdFile = $service->files->create($file, array(
          'data' => $data,
          'mimeType' => 'image/jpeg',
          'uploadType' => 'multipart'
        ));

    print_r($createdFile);

} else {
    $authUrl = $client->createAuthUrl();
    header('Location: ' . $authUrl);
    exit();
}
