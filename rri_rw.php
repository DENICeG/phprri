<?php
// Voraussetzung: sudo apt-get install php5-cli
 
$rriSocketAddress = "ssl://rri.test.denic.de:51131";
 
$conn = stream_socket_client("$rriSocketAddress", $errno, $errstr);
 
//Senden von Daten:
function RRI_Send($conn, $order)
{
    $len=strlen($order);
    $nlen=RRI_Pack($len);                       // Convert Bytes of len to Network-Byte-Order
    $bytes=fwrite($conn,$nlen,4);               // send length of order to server
    $bytes_sent=fwrite($conn,$order,$len);      // send order
    echo "Number of sent bytes: " . $bytes_sent;
    return $bytes_sent;
}
 
function RRI_Pack($len)
{
    $pack = pack("N",$len); // format type Long (4 bytes) -> pack len into binary string
    return $pack;
     
}
 
//Lesen von Daten:
function RRI_Read($conn)
{
    $nlen=fread($conn,4);       // read 4-Byte length of answer
    $bytes=RRI_Unpack($nlen);   // convert bytes to local order
    echo "\nNumber of expected bytes: " . $bytes;
    $answer="";
    $a=fread($conn,$bytes);  // read answer
    $answer.=$a;
    $gelesen=strlen($a);
    echo "\nNumber of received bytes: " . $gelesen;
    return $answer;
}
 
function RRI_Unpack($len)
{
    $unpack = unpack("N",$len); // unpack binary string into Long (4 bytes)
    return $unpack[1];
     
}
 
function handle_RRI_orders($conn, $orders, $outputFile)
{
    $splittedOrders = explode("=-=\n", $orders);
     
    foreach ($splittedOrders as $singleOrder)
        {
            echo "\nOrder:\n" . $singleOrder;
            RRI_Send($conn, $singleOrder);
            $answer = RRI_Read($conn);
            fwrite($outputFile, $answer . "\n=-=\n");
            echo "\nAnswer:\n" . $answer;
        }
}
 
$myOrdersFile = fopen("orders.rri", "r") or die("Unable to open orders file!");
$myAnswersFile = fopen("answers.rri", "w") or die("Unable to open file for output!");
 
$orders = fread($myOrdersFile,filesize("orders.rri"));
handle_RRI_orders($conn, $orders, $myAnswersFile);
fclose($myOrdersFile);
fclose($myAnswersFile);
?>
