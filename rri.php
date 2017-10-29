//Lesen von Daten:
function RRI_Read($conn)
{
    $nlen=fread($conn,4);	// read 4-Byte length of answer 
    $bytes=RRI_Unpack($nlen);	// convert bytes to local order
    $rest=$bytes;
    $answer="";
    while ($rest) {
        $a=fread($conn,$rest);	// read answer
        $answer.=$a;
        $gelesen=strlen($a);
        $rest-=$gelesen;
        if (feof($conn)) {
            break;
        }
    }
    return $answer;
}


//Senden von Daten:
function RRI_Send($conn, $order)
{
    $len=strlen($order);
    $nlen=RRI_Pack($len);	// Convert Bytes of len to Network-Byte-Order
    $bytes=fwrite($conn,$nlen,4);	// send length of order to server
    $bytes_send=fwrite($conn,$order,$len); 	// send order
    return $bytes_send;
}
