<?php

function getUserAvatar($userId) {
    $ch = curl_init("https://discord.com/api/v9/users/" . $userId);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bot " . "MTQyODcxNjU0MjcwMjM4NzI4MQ.GGKDgI.ogPnThsJc8j4HQAkK7q4O6xszLE-NNQYKZ-Kbs"]);
    $json = curl_exec($ch);
    curl_close($ch);
    $obj = json_decode($json);
    $id = $obj->id;
    $avatar = $obj->avatar;
    $ext = str_starts_with($avatar, "a_") ? "gif" : "png";
	
	if (empty($avatar)) {
     return "https://cdn.discordapp.com/embed/avatars/2.png";
    } 
	else
	{
		 return "https://cdn.discordapp.com/avatars/{$id}/{$avatar}.{$ext}";
	}
   
}


echo getUserAvatar("1412463759049625760");

?>
