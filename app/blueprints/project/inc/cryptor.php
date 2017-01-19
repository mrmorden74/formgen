<?php
// Original PHP code by Chirp Internet: www.chirp.com.au
// Please acknowledge use of this code by including this header.

class Cryptor
{

  protected $method = 'AES-128-CTR'; // default
  private $key;

  protected function iv_bytes()
  {
    return openssl_cipher_iv_length($this->method);
  }

  public function __construct($key = false, $method = 'AES-128-CTR')
  {
    if(!$key) {
      // if you don't supply your own key, this will be the default
      $key = gethostname() . "|" . ip2long($_SERVER['SERVER_ADDR']);
    }
    if(ctype_print($key)) {
      // convert key to binary format
      $this->key = openssl_digest($key, 'SHA256', true);
    } else {
      $this->key = $key;
    }
    if($method) {
      if(in_array($method, openssl_get_cipher_methods())) {
        $this->method = $method;
      } else {
        die(__METHOD__ . ": unrecognised encryption method: {$method}");
      }
    }
  }

  public function encrypt($data)
  {
    $iv = openssl_random_pseudo_bytes($this->iv_bytes());
    $encrypted_string = bin2hex($iv) . openssl_encrypt($data, $this->method, $this->key, 0, $iv);
    return $encrypted_string;
  }

  // decrypt encrypted string
  public function decrypt($data)
  {
    $iv_strlen = 2  * $this->iv_bytes();
    // echo 'iv_strlen:'.$iv_strlen.'<br>';
    // echo 'data:'.$data.'<br>';
    if(preg_match("/^(.{" . $iv_strlen . "})(.+)$/", $data, $regs)) {
    //   echo 'regs:';
    // var_dump ($regs);
    //   echo 'OK'.'<br>';
      list(, $iv, $crypted_string) = $regs;
      $decrypted_string = openssl_decrypt($crypted_string, $this->method, $this->key, 0, hex2bin($iv));
      // echo 'decrypted: '.$decrypted_string;
      return $decrypted_string;
    } else {
      return false;
    }
  }

}