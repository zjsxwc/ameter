<?php

date_default_timezone_set('Asia/Shanghai');

/**
 * @param string $deviceId
 * @param string $password
 * @return bool|resource
 */
function getRegisteredSocket($deviceId = "51903220020066", $password = "12345678") {
    $isRegistered = false;

    $socket = socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
    if(socket_connect($socket,'120.26.165.200',8001) == false){
        echo 'connect fail massege:'.socket_strerror(socket_last_error());
    }else{
        //向服务端写入字符串信息
        $id = $deviceId;
        while (strlen($id) != 20) {
            $id .= "\x00";
        }
        $pw = $password;
        while (strlen($pw) != 10) {
            $pw .= "\x00";
        }
        $message = "\x3e\x40" . "id:" . $id . "pw:" . $pw;
        if(socket_write($socket,$message,strlen($message)) == false){
            echo 'fail to write'.socket_strerror(socket_last_error());

        }else{
            $callback = @socket_read($socket,5);
            $resultArray = @unpack("H4CODE/a3MSG", $callback);
            if (
                isset($resultArray["CODE"]) && ($resultArray["CODE"] == "3c40")
                && isset($resultArray["MSG"]) && ($resultArray["MSG"] == "ok!")
            ) {
                $isRegistered = true;
            }
        }
    }
    if ($isRegistered) {
        return $socket;
    } else {
        return false;
    }

}


/**
 * @param string $deviceId
 * @param string $password
 * @param null $returnValue
 * @return bool
 */
function getLdwd($deviceId = "51903220020066", $password = "12345678", &$returnValue = null) {
    $socket = getRegisteredSocket($deviceId, $password);
    if (!$socket) {
        echo "socket err failed getLdwd\n";
        return false;
    }
    $isDeviceOnline = false;
    $message = "\x3e\x40" . "online?" . "\x00";
    if(socket_write($socket,$message,strlen($message)) == false){
        echo 'fail to write'.socket_strerror(socket_last_error());

    }else{
        $callback = @socket_read($socket,5);
        $resultArray = @unpack("H4CODE/a3MSG", $callback);

        if (
            isset($resultArray["CODE"]) && ($resultArray["CODE"] == "3c40")
            && isset($resultArray["MSG"]) && ($resultArray["MSG"] == "yes")
        ) {
            $isDeviceOnline = true;
        }
    }
    if (!$isDeviceOnline) {
        echo "device online err failed getLdwd\n";
        socket_close($socket);
        return false;
    }


    echo "尝试获取 漏电温度 \n";
    //发送设备通讯报文漏电温度
    $message = "\x7b\x7b\x90\x01\x03\x10\x00\x00\x2a\xc0\xd5\xe6\xfd\x7d\x7d";
    if(socket_write($socket,$message,strlen($message)) == false){
        echo 'fail to write'.socket_strerror(socket_last_error());
    }else{
        sleep(1);
        while($callback = @socket_read($socket,1024)){
            if (!$callback) {
                continue;
            }

            if (
                strlen($callback) == 96 && ord($callback{2}) == 0x90
            ) {
                $returnValue = $callback;
                file_put_contents(__DIR__ . "/ldwd.example", serialize($callback));
                socket_close($socket);
                return true;
            }

            //socket_write($socket,$message,strlen($message));
        }
    }

    socket_close($socket);
    return false;
}


/**
 * @param string $deviceId
 * @param string $password
 * @param null $returnValue
 * @return bool
 */
function getDldy($deviceId = "51903220020066", $password = "12345678", &$returnValue = null) {
    $socket = getRegisteredSocket($deviceId, $password);
    if (!$socket) {
        echo "socket err failed getLdwd\n";
        return false;
    }
    $isDeviceOnline = false;
    $message = "\x3e\x40" . "online?" . "\x00";
    if(socket_write($socket,$message,strlen($message)) == false){
        echo 'fail to write'.socket_strerror(socket_last_error());

    }else{
        $callback = @socket_read($socket,5);
        $resultArray = @unpack("H4CODE/a3MSG", $callback);

        if (
            isset($resultArray["CODE"]) && ($resultArray["CODE"] == "3c40")
            && isset($resultArray["MSG"]) && ($resultArray["MSG"] == "yes")
        ) {
            $isDeviceOnline = true;
        }
    }
    if (!$isDeviceOnline) {
        echo "device online err failed getDldy\n";
        socket_close($socket);
        return false;
    }


    echo "尝试获取 电压电流 \n";
    //发送设备通讯报文 电压电流
    $message = "\x7b\x7b\x90\x01\x03\x12\x04\x00\x1a\x80\xb8\xe6\xfd\x7d\x7d";
    if(socket_write($socket,$message,strlen($message)) == false){
        echo 'fail to write'.socket_strerror(socket_last_error());
    }else{
        sleep(1);
        while($callback = @socket_read($socket,1024)){
            if (!$callback) {
                continue;
            }

            if (
                strlen($callback) == 64 && ord($callback{2}) == 0x90
            ) {
                $returnValue = $callback;
                file_put_contents(__DIR__ . "/dldy.example", serialize($callback));
                socket_close($socket);
                return true;
            }
            //socket_write($socket,$message,strlen($message));
        }
    }

    socket_close($socket);
    return false;
}


/**
 * @param string $deviceId
 * @param string $password
 * @return bool
 */
function getDn($deviceId = "51903220020066", $password = "12345678", &$returnValue = null) {
    $socket = getRegisteredSocket($deviceId, $password);
    if (!$socket) {
        echo "socket err failed getLdwd\n";
        return false;
    }
    $isDeviceOnline = false;
    $message = "\x3e\x40" . "online?" . "\x00";
    if(socket_write($socket,$message,strlen($message)) == false){
        echo 'fail to write'.socket_strerror(socket_last_error());

    }else{
        $callback = @socket_read($socket,5);
        $resultArray = @unpack("H4CODE/a3MSG", $callback);

        if (
            isset($resultArray["CODE"]) && ($resultArray["CODE"] == "3c40")
            && isset($resultArray["MSG"]) && ($resultArray["MSG"] == "yes")
        ) {
            $isDeviceOnline = true;
        }
    }
    if (!$isDeviceOnline) {
        echo "device online err failed getDn\n";
        socket_close($socket);
        return false;
    }

    echo "尝试获取 电能 \n";
    //发送设备通讯报文 电压电流
    $message = "\x7b\x7b\x90\x01\x03\x13\x00\x00\x02\xc0\x8f\xe6\xfd\x7d\x7d";
    if(socket_write($socket,$message,strlen($message)) == false){
        echo 'fail to write'.socket_strerror(socket_last_error());
    }else{
        sleep(1);
        while($callback = @socket_read($socket,1024)){
            if (!$callback) {
                continue;
            }
            if (
                strlen($callback) == 16 && ord($callback{2}) == 0x90
            ) {
                $returnValue = $callback;
                file_put_contents(__DIR__ . "/dn.example", serialize($callback));
                socket_close($socket);
                return true;
            }
            //socket_write($socket,$message,strlen($message));
        }
    }


    socket_close($socket);
    return false;
}




/**
 * 01字符串 转换为 整数
 * @param string $str
 * @param int $bitsCount
 * @return int
 */
function unsignedBinStrToInt($str, $bitsCount = 8) {
    if (strlen($str) != $bitsCount) {
        throw new \RuntimeException("bitsCount error");
    }
    $int = null;
    eval("\$int = 0b". $str.";");
    return $int;
}
/**
 * 01字符串 转换为 有符号整数
 * @param string $str
 * @param int $bitsCount
 * @return int
 */
function signedBinStrToInt($str, $bitsCount = 8) {
    if (strlen($str) != $bitsCount) {
        throw new \RuntimeException("bitsCount error");
    }

    if (intval($str{0}) == 0) {
        return unsignedBinStrToInt($str, $bitsCount);
    }
    // 无符号数 就是  有符号负数的 补码， 那么根据  有符号负数的绝对值  加上 它自己的补码，  就等于 溢出一位 ，也就是2的8次 256， 于是  有符号负数 等于  无符号数 减去 256
    return intval(unsignedBinStrToInt($str, $bitsCount) - pow(2, $bitsCount));
}



/**
 * @param string $data
 * @return mixed
 */
function parseLdwd($data) {
    $isValid = $data{0} == "{" &&
        $data{1} == "{" &&
        ord($data{2}) == 0x90 &&
        ord($data{3}) == 0x01 &&
        ord($data{4}) == 0x03 &&
        ord($data{5}) == 0x54;
    if (!$isValid) {
        echo "不是有效的Ldwd数据\n";
        return false;
    }

    $dataStartPos = 6;
    $byteOffset = 0;
    $maxByteOffset = 84 - 1; //总共84字节，因为从0开始所以 最大偏移是 83

    //通道类别
    echo "\n通道类别:\n";
    $offset = 0x00;
    $byteOffset = $offset * 2;
    $h = ord($data{$dataStartPos + $byteOffset});
    printf("%08b ", $h);
    $l = ord($data{$dataStartPos + $byteOffset + 1});
    printf("%08b ", $l);
    // $h 与 $l 总共16个bit， 每个bit值0表示电流检测回路 值1表示温度检测回路 ， $l 最 右边的bit表示回路1  $h 最左边的 bit 表示回路16


    //断线
    echo "\n断线:\n";
    $offset = 0x01;
    $byteOffset = $offset * 2;
    $h = ord($data{$dataStartPos + $byteOffset});
    printf("%08b ", $h);
    $l = ord($data{$dataStartPos + $byteOffset + 1});
    printf("%08b ", $l);
    // $h 与 $l 总共16个bit， 每个bit值0表示回路正常 值1表示回路断线 ， $l 最 右边的bit表示回路1  $h 最左边的 bit 表示回路16


    //短路
    echo "\n短路:\n";
    $offset = 0x02;
    $byteOffset = $offset * 2;
    $h = ord($data{$dataStartPos + $byteOffset});
    printf("%08b ", $h);
    $l = ord($data{$dataStartPos + $byteOffset + 1});
    printf("%08b ", $l);
    // $h 与 $l 总共16个bit， 每个bit值0表示回路正常 值1表示回路短路 ， $l 最 右边的bit表示回路1  $h 最左边的 bit 表示回路16


    //报警状态
    echo "\n报警状态:\n";
    $offset = 0x03;
    $byteOffset = $offset * 2;
    $h = ord($data{$dataStartPos + $byteOffset});
    printf("%08b ", $h);
    $l = ord($data{$dataStartPos + $byteOffset + 1});
    printf("%08b ", $l);
    // $h 与 $l 总共16个bit， 每个bit值0表示回路正常 值1表示回路报警 ， $l 最 右边的bit表示回路1  $h 最左边的 bit 表示回路16


    //漏电测量值
    echo "\n漏电测量值:\n";
    $offset = 0x05;
    $byteOffset = $offset * 2;
    $h = ord($data{$dataStartPos + $byteOffset});
    $l = ord($data{$dataStartPos + $byteOffset + 1});
    $value = unsignedBinStrToInt(sprintf("%08b", $h) .  sprintf("%08b", $l), 16); //数值换算翻转高低位
    printf("%d (0b%08b 0b%08b)", $value, $h, $l);


    echo "\n第一路温度测量值:\n";
    $offset = 0x06;
    $byteOffset = $offset * 2;
    $h = ord($data{$dataStartPos + $byteOffset});
    $l = ord($data{$dataStartPos + $byteOffset + 1});
    $value = signedBinStrToInt(sprintf("%08b", $h) .  sprintf("%08b", $l), 16); //数值换算翻转高低位
    printf("%.1f (0b%08b 0b%08b)", $value/10.0, $h, $l);

    echo "\n第二路温度测量值:\n";
    $offset = 0x07;
    $byteOffset = $offset * 2;
    $h = ord($data{$dataStartPos + $byteOffset});
    $l = ord($data{$dataStartPos + $byteOffset + 1});
    $value = signedBinStrToInt(sprintf("%08b", $h) .  sprintf("%08b", $l), 16); //数值换算翻转高低位
    printf("%.1f (0b%08b 0b%08b)", $value/10.0, $h, $l);

    echo "\n第三路温度测量值:\n";
    $offset = 0x08;
    $byteOffset = $offset * 2;
    $h = ord($data{$dataStartPos + $byteOffset});
    $l = ord($data{$dataStartPos + $byteOffset + 1});
    $value = signedBinStrToInt(sprintf("%08b", $h) .  sprintf("%08b", $l), 16); //数值换算翻转高低位
    printf("%.1f (0b%08b 0b%08b)", $value/10.0, $h, $l);


    echo "\n第四路温度测量值:\n";
    $offset = 0x09;
    $byteOffset = $offset * 2;
    $h = ord($data{$dataStartPos + $byteOffset});
    $l = ord($data{$dataStartPos + $byteOffset + 1});
    $value = signedBinStrToInt(sprintf("%08b", $h) .  sprintf("%08b", $l), 16); //数值换算翻转高低位
    printf("%.1f (0b%08b 0b%08b)", $value/10.0, $h, $l);


    echo "\n漏电报警测量值:\n";
    $offset = 0x15;
    $byteOffset = $offset * 2;
    $h = ord($data{$dataStartPos + $byteOffset});
    $l = ord($data{$dataStartPos + $byteOffset + 1});
    $value = unsignedBinStrToInt(sprintf("%08b", $h) .  sprintf("%08b", $l), 16); //数值换算翻转高低位
    printf("%d (0b%08b 0b%08b)", $value, $h, $l);


    echo "\n第一路温度报警值:\n";
    $offset = 0x16;
    $byteOffset = $offset * 2;
    $h = ord($data{$dataStartPos + $byteOffset});
    $l = ord($data{$dataStartPos + $byteOffset + 1});
    $value = signedBinStrToInt(sprintf("%08b", $h) .  sprintf("%08b", $l), 16); //数值换算翻转高低位
    printf("%.1f (0b%08b 0b%08b)", $value/10.0, $h, $l);


    echo "\n第二路温度报警值:\n";
    $offset = 0x17;
    $byteOffset = $offset * 2;
    $h = ord($data{$dataStartPos + $byteOffset});
    $l = ord($data{$dataStartPos + $byteOffset + 1});
    $value = signedBinStrToInt(sprintf("%08b", $h) .  sprintf("%08b", $l), 16); //数值换算翻转高低位
    printf("%.1f (0b%08b 0b%08b)", $value/10.0, $h, $l);

    echo "\n第三路温度报警值:\n";
    $offset = 0x18;
    $byteOffset = $offset * 2;
    $h = ord($data{$dataStartPos + $byteOffset});
    $l = ord($data{$dataStartPos + $byteOffset + 1});
    $value = signedBinStrToInt(sprintf("%08b", $h) .  sprintf("%08b", $l), 16); //数值换算翻转高低位
    printf("%.1f (0b%08b 0b%08b)", $value/10.0, $h, $l);

    echo "\n第四路温度报警值:\n";
    $offset = 0x19;
    $byteOffset = $offset * 2;
    $h = ord($data{$dataStartPos + $byteOffset});
    $l = ord($data{$dataStartPos + $byteOffset + 1});
    $value = signedBinStrToInt(sprintf("%08b", $h) .  sprintf("%08b", $l), 16); //数值换算翻转高低位
    printf("%.1f (0b%08b 0b%08b)", $value/10.0, $h, $l);



    //开入DI
    echo "\n开入DI:\n";
    $offset = 0x28;
    $byteOffset = $offset * 2;
    $h = ord($data{$dataStartPos + $byteOffset});
    printf("%08b ", $h);
    $l = ord($data{$dataStartPos + $byteOffset + 1});
    printf("%08b ", $l);
    // $h 与 $l 总共16个bit， 每个bit值0表示DI打开 值1表示DI闭合 ， $l 最 右边的bit表示DI1  $h 最左边的 bit 表示DI16


    //开出DO
    echo "\n开出DO:\n";
    $offset = 0x29;
    $byteOffset = $offset * 2;
    $h = ord($data{$dataStartPos + $byteOffset});
    printf("%08b ", $h);
    $l = ord($data{$dataStartPos + $byteOffset + 1});
    printf("%08b ", $l);
    // $h 与 $l 总共16个bit， 每个bit值0表示DO闭合 值1表示DO打开 ， $l 最 右边的bit表示DO1  $h 最左边的 bit 表示DO16


    echo "\n";
}


/**
 * @param string $data
 * @return mixed
 */
function parseDldy($data) {
    $isValid = $data{0} == "{" &&
        $data{1} == "{" &&
        ord($data{2}) == 0x90 &&
        ord($data{3}) == 0x01 &&
        ord($data{4}) == 0x03 &&
        ord($data{5}) == 0x34;
    if (!$isValid) {
        echo "不是有效的Dldy数据\n";
        return false;
    }

    $dataStartPos = 6;
    $byteOffset = 0;
    $maxByteOffset = 64 - 1; //总共64字节，因为从0开始所以 最大偏移是 63

    echo "\nA相相电压（V）:\n";
    $offset = 0;
    $byteOffset = $offset * 2;
    $h = ord($data{$dataStartPos + $byteOffset});
    $l = ord($data{$dataStartPos + $byteOffset + 1});
    $value = unsignedBinStrToInt(sprintf("%08b", $h) .  sprintf("%08b", $l), 16); //数值换算翻转高低位
    printf("%.1f (0b%08b 0b%08b)", $value/10.0, $h, $l);

    echo "\nB相相电压（V）:\n";
    $offset = 1;
    $byteOffset = $offset * 2;
    $h = ord($data{$dataStartPos + $byteOffset});
    $l = ord($data{$dataStartPos + $byteOffset + 1});
    $value = unsignedBinStrToInt(sprintf("%08b", $h) .  sprintf("%08b", $l), 16); //数值换算翻转高低位
    printf("%.1f (0b%08b 0b%08b)", $value/10.0, $h, $l);


    echo "\nC相相电压（V）:\n";
    $offset = 2;
    $byteOffset = $offset * 2;
    $h = ord($data{$dataStartPos + $byteOffset});
    $l = ord($data{$dataStartPos + $byteOffset + 1});
    $value = unsignedBinStrToInt(sprintf("%08b", $h) .  sprintf("%08b", $l), 16); //数值换算翻转高低位
    printf("%.1f (0b%08b 0b%08b)", $value/10.0, $h, $l);


    echo "\nUAB线电压（V）:\n";
    $offset = 3;
    $byteOffset = $offset * 2;
    $h = ord($data{$dataStartPos + $byteOffset});
    $l = ord($data{$dataStartPos + $byteOffset + 1});
    $value = unsignedBinStrToInt(sprintf("%08b", $h) .  sprintf("%08b", $l), 16); //数值换算翻转高低位
    printf("%.1f (0b%08b 0b%08b)", $value/10.0, $h, $l);


    echo "\nUBC线电压（V）:\n";
    $offset = 4;
    $byteOffset = $offset * 2;
    $h = ord($data{$dataStartPos + $byteOffset});
    $l = ord($data{$dataStartPos + $byteOffset + 1});
    $value = unsignedBinStrToInt(sprintf("%08b", $h) .  sprintf("%08b", $l), 16); //数值换算翻转高低位
    printf("%.1f (0b%08b 0b%08b)", $value/10.0, $h, $l);



    echo "\nUCA线电压（V）:\n";
    $offset = 5;
    $byteOffset = $offset * 2;
    $h = ord($data{$dataStartPos + $byteOffset});
    $l = ord($data{$dataStartPos + $byteOffset + 1});
    $value = unsignedBinStrToInt(sprintf("%08b", $h) .  sprintf("%08b", $l), 16); //数值换算翻转高低位
    printf("%.1f (0b%08b 0b%08b)", $value/10.0, $h, $l);



    echo "\n电压状态位:\n";
    $offset = 9;
    $byteOffset = $offset * 2;
    $h = ord($data{$dataStartPos + $byteOffset});
    $l = ord($data{$dataStartPos + $byteOffset + 1});
    printf("高字节 0x%02x 低字节 0x%02x\n", $h, $l);


    echo "\nA相过压值（V）:\n";
    $offset = 10;
    $byteOffset = $offset * 2;
    $h = ord($data{$dataStartPos + $byteOffset});
    $l = ord($data{$dataStartPos + $byteOffset + 1});
    $value = unsignedBinStrToInt(sprintf("%08b", $h) .  sprintf("%08b", $l), 16); //数值换算翻转高低位
    printf("%.1f (0b%08b 0b%08b)", $value/10.0, $h, $l);

    echo "\nB相过压值（V）:\n";
    $offset = 11;
    $byteOffset = $offset * 2;
    $h = ord($data{$dataStartPos + $byteOffset});
    $l = ord($data{$dataStartPos + $byteOffset + 1});
    $value = unsignedBinStrToInt(sprintf("%08b", $h) .  sprintf("%08b", $l), 16); //数值换算翻转高低位
    printf("%.1f (0b%08b 0b%08b)", $value/10.0, $h, $l);

    echo "\nC相过压值（V）:\n";
    $offset = 12;
    $byteOffset = $offset * 2;
    $h = ord($data{$dataStartPos + $byteOffset});
    $l = ord($data{$dataStartPos + $byteOffset + 1});
    $value = unsignedBinStrToInt(sprintf("%08b", $h) .  sprintf("%08b", $l), 16); //数值换算翻转高低位
    printf("%.1f (0b%08b 0b%08b)", $value/10.0, $h, $l);

    echo "\nA相欠压值（V）:\n";
    $offset = 13;
    $byteOffset = $offset * 2;
    $h = ord($data{$dataStartPos + $byteOffset});
    $l = ord($data{$dataStartPos + $byteOffset + 1});
    $value = unsignedBinStrToInt(sprintf("%08b", $h) .  sprintf("%08b", $l), 16); //数值换算翻转高低位
    printf("%.1f (0b%08b 0b%08b)", $value/10.0, $h, $l);

    echo "\nB相欠压值（V）:\n";
    $offset = 14;
    $byteOffset = $offset * 2;
    $h = ord($data{$dataStartPos + $byteOffset});
    $l = ord($data{$dataStartPos + $byteOffset + 1});
    $value = unsignedBinStrToInt(sprintf("%08b", $h) .  sprintf("%08b", $l), 16); //数值换算翻转高低位
    printf("%.1f (0b%08b 0b%08b)", $value/10.0, $h, $l);

    echo "\nC相欠压值（V）:\n";
    $offset = 15;
    $byteOffset = $offset * 2;
    $h = ord($data{$dataStartPos + $byteOffset});
    $l = ord($data{$dataStartPos + $byteOffset + 1});
    $value = unsignedBinStrToInt(sprintf("%08b", $h) .  sprintf("%08b", $l), 16); //数值换算翻转高低位
    printf("%.1f (0b%08b 0b%08b)", $value/10.0, $h, $l);


    echo "\nA相电流（A）:\n";
    $offset = 16;
    $byteOffset = $offset * 2;
    $h = ord($data{$dataStartPos + $byteOffset});
    $l = ord($data{$dataStartPos + $byteOffset + 1});
    $value = unsignedBinStrToInt(sprintf("%08b", $h) .  sprintf("%08b", $l), 16); //数值换算翻转高低位
    printf("%.3f (0b%08b 0b%08b)", $value/1000.0, $h, $l);


    echo "\nB相电流（A）:\n";
    $offset = 17;
    $byteOffset = $offset * 2;
    $h = ord($data{$dataStartPos + $byteOffset});
    $l = ord($data{$dataStartPos + $byteOffset + 1});
    $value = unsignedBinStrToInt(sprintf("%08b", $h) .  sprintf("%08b", $l), 16); //数值换算翻转高低位
    printf("%.3f (0b%08b 0b%08b)", $value/1000, $h, $l);

    echo "\nC相电流（A）:\n";
    $offset = 18;
    $byteOffset = $offset * 2;
    $h = ord($data{$dataStartPos + $byteOffset});
    $l = ord($data{$dataStartPos + $byteOffset + 1});
    $value = unsignedBinStrToInt(sprintf("%08b", $h) .  sprintf("%08b", $l), 16); //数值换算翻转高低位
    printf("%.3f (0b%08b 0b%08b)", $value/1000, $h, $l);



    echo "\n电流状态:\n";
    $offset = 22;
    $byteOffset = $offset * 2;
    $h = ord($data{$dataStartPos + $byteOffset});
    $l = ord($data{$dataStartPos + $byteOffset + 1});
    printf("0x%02x 0x%02x", $h, $l);


    echo "\nA相过流值（A）:\n";
    $offset = 23;
    $byteOffset = $offset * 2;
    $h = ord($data{$dataStartPos + $byteOffset});
    $l = ord($data{$dataStartPos + $byteOffset + 1});
    $value = unsignedBinStrToInt(sprintf("%08b", $h) .  sprintf("%08b", $l), 16); //数值换算翻转高低位
    printf("%.3f (0b%08b 0b%08b)", $value/1000, $h, $l);

    echo "\nB相过流值（A）:\n";
    $offset = 24;
    $byteOffset = $offset * 2;
    $h = ord($data{$dataStartPos + $byteOffset});
    $l = ord($data{$dataStartPos + $byteOffset + 1});
    $value = unsignedBinStrToInt(sprintf("%08b", $h) .  sprintf("%08b", $l), 16); //数值换算翻转高低位
    printf("%.3f (0b%08b 0b%08b)", $value/1000, $h, $l);

    echo "\nC相过流值（A）:\n";
    $offset = 25;
    $byteOffset = $offset * 2;
    $h = ord($data{$dataStartPos + $byteOffset});
    $l = ord($data{$dataStartPos + $byteOffset + 1});
    $value = unsignedBinStrToInt(sprintf("%08b", $h) .  sprintf("%08b", $l), 16); //数值换算翻转高低位
    printf("%.3f (0b%08b 0b%08b)", $value/1000, $h, $l);

    echo "\n";
}



/**
 * @param string $data
 * @return mixed
 */
function parseDn($data) {
    $isValid = $data{0} == "{" &&
        $data{1} == "{" &&
        ord($data{2}) == 0x90 &&
        ord($data{3}) == 0x01 &&
        ord($data{4}) == 0x03 &&
        ord($data{5}) == 0x04;
    if (!$isValid) {
        echo "不是有效的Dn数据\n";
        return false;
    }


    $dataStartPos = 6;

    echo "\n吸收有功功率（kWh）:\n";
    $offset = 0;
    $byteOffset = $offset * 2;
    $h1 = ord($data{$dataStartPos + $byteOffset});
    $l1 = ord($data{$dataStartPos + $byteOffset + 1});
    $offset = 1;
    $byteOffset = $offset * 2;
    $h2 = ord($data{$dataStartPos + $byteOffset});
    $l2 = ord($data{$dataStartPos + $byteOffset + 1});
    printf("0x%02x 0x%02x 0x%02x 0x%02x \n", $h1, $l1, $h2, $l2);

    echo "\n";

}



$deviceId = "51903220020066";
$password = "12345678";
$returnValue = null;

echo <<<HTML
    <!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>电表数据获取测试</title>
</head>
<body><pre>
HTML;


printf("本次访问尝试获取设备 %s 的数据，日期%s\n", $deviceId, date("Y-m-d H:i:s"));
printf("\n如果获取失败请重新刷新本页面\n\n\n");


if (getLdwd($deviceId, $password, $returnValue)) {
    parseLdwd($returnValue);
} else {
    echo "获取漏电温度失败 \n\n";
}
if (getDldy($deviceId, $password, $returnValue)) {
    parseDldy($returnValue);
} else {
    echo "获取电流电压失败 \n\n";
}
if (getDn($deviceId, $password, $returnValue)) {
    parseDn($returnValue);
} else {
    echo "获取电能失败 \n\n";
}



echo <<<HTML
</pre>
</body>
</html>
HTML;




