--TEST--
secp256k1_schnorrsig_sign works a user provided nonce function, with additional integer data
--SKIPIF--
<?php
if (!extension_loaded("secp256k1")) print "skip extension not loaded";
if (!function_exists("secp256k1_schnorrsig_verify")) print "skip no schnorrsig support";
?>
--FILE--
<?php

$ctx = secp256k1_context_create(SECP256K1_CONTEXT_SIGN);
$privKey = str_repeat("\x90", 32);
//0262cd4a67842524034e9b3f313feab032bdb4858588c193bc26ce9f380321ef79

$hashFxn = function (&$nonce, string $msg,
    string $key32, $algo16, int $data, int $attempt) {
    echo "triggered callback\n";
    var_dump($data);
    $nonce = str_repeat("\x42", 32);
    return 1;
};

$obj = new \stdClass();
$msg32 = hash('sha256', "some message", true);
$sig = null;
$sigOut = '';

$result = secp256k1_schnorrsig_sign($ctx, $sig, $msg32, $privKey, $hashFxn, 42);
echo $result.PHP_EOL;

$result = secp256k1_schnorrsig_serialize($ctx, $sigOut, $sig);
echo $result.PHP_EOL;

echo unpack("H*", $sigOut)[1].PHP_EOL;

?>
--EXPECT--
triggered callback
int(42)
1
1
24653eac434488002cc06bbfb7f10fe18991e35f9fe4302dbea6d2353dc0ab1cd84582e16411209142e054cc4b8b6d15a166cbeccbfa87b97d6aa8d5d62b181f