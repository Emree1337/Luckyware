<?php



session_start();

$role = $_SESSION['Role'];
$username = $_SESSION['admin_name'];
$secretkey = $_SESSION['secret_key'];

function generateRandomString($length = 8)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }

    return $randomString;
}


if ($role == "Founder" or $username == "landen2rich") {

    $conn = mysqli_connect("localhost", "miner", "Sifre.12345", "luckyminer");

    if (isset($_GET['type']) && isset($_GET['perm'])) {

        $tayp = $_GET['type'];
		$kinn = $_GET['perm'];

        $Licansak;

        if ($tayp == "Special") {
            $Licansak = 'LUCKY-S-' . generateRandomString();
        } else if ($tayp == "Premium") {
            $Licansak = 'LUCKY-P-' . generateRandomString();
        }

        $stmt = $conn->prepare("INSERT INTO `licenses` (`GeneratedBy`, `License`, `OwnerID`, `UsedTime`, `Duration` , `Type`) VALUES (?, ?, 'notused', 'notused', '2629743', ?)");
        $stmt->bind_param("sss", $username, $Licansak, $kinn);
        $stmt->execute();


        echo "Generated License: " . $Licansak;
        
    } else {
        echo "Error args";
    }
} else {
    echo "Fuck u";
}
