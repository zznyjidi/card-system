<?php
namespace App\Library\Geetest; class Lib { const GT_SDK_VERSION = 'php_3.2.0'; public static $connectTimeout = 1; public static $socketTimeout = 1; private $response; public $captcha_id; public $private_key; public function __construct($sp786383, $spc23e50) { $this->captcha_id = $sp786383; $this->private_key = $spc23e50; } public function pre_process($spc2138c = null) { $sp8042f4 = 'http://api.geetest.com/register.php?gt=' . $this->captcha_id; if ($spc2138c != null and is_string($spc2138c)) { $sp8042f4 = $sp8042f4 . '&user_id=' . $spc2138c; } $sp9cf920 = $this->send_request($sp8042f4); if (strlen($sp9cf920) != 32) { $this->failback_process(); return 0; } $this->success_process($sp9cf920); return 1; } private function success_process($sp9cf920) { $sp9cf920 = md5($sp9cf920 . $this->private_key); $sp11ac9a = array('success' => 1, 'gt' => $this->captcha_id, 'challenge' => $sp9cf920); $this->response = $sp11ac9a; } private function failback_process() { $sp9a2164 = md5(rand(0, 100)); $spe52414 = md5(rand(0, 100)); $sp9cf920 = $sp9a2164 . substr($spe52414, 0, 2); $sp11ac9a = array('success' => 0, 'gt' => $this->captcha_id, 'challenge' => $sp9cf920); $this->response = $sp11ac9a; } public function get_response_str() { return json_encode($this->response); } public function get_response() { return $this->response; } public function success_validate($sp9cf920, $spe95d36, $spf8502e, $spc2138c = null) { if (!$this->check_validate($sp9cf920, $spe95d36)) { return 0; } $spf588a3 = array('seccode' => $spf8502e, 'sdk' => self::GT_SDK_VERSION); if ($spc2138c != null and is_string($spc2138c)) { $spf588a3['user_id'] = $spc2138c; } $sp8042f4 = 'http://api.geetest.com/validate.php'; $spf61788 = $this->post_request($sp8042f4, $spf588a3); if ($spf61788 == md5($spf8502e)) { return 1; } else { if ($spf61788 == 'false') { return 0; } else { return 0; } } } public function fail_validate($sp9cf920, $spe95d36, $spf8502e) { if ($spe95d36) { $sp9d1f75 = explode('_', $spe95d36); try { $sp2d5350 = $this->decode_response($sp9cf920, $sp9d1f75['0']); $spe0df4c = $this->decode_response($sp9cf920, $sp9d1f75['1']); $spfc0486 = $this->decode_response($sp9cf920, $sp9d1f75['2']); $spb611a3 = $this->get_failback_pic_ans($spe0df4c, $spfc0486); $sp0e6201 = abs($sp2d5350 - $spb611a3); } catch (\Exception $sp45222f) { return 1; } if ($sp0e6201 < 4) { return 1; } else { return 0; } } else { return 0; } } private function check_validate($sp9cf920, $spe95d36) { if (strlen($spe95d36) != 32) { return false; } if (md5($this->private_key . 'geetest' . $sp9cf920) != $spe95d36) { return false; } return true; } private function send_request($sp8042f4) { if (function_exists('curl_exec')) { $spee7fa4 = curl_init(); curl_setopt($spee7fa4, CURLOPT_URL, $sp8042f4); curl_setopt($spee7fa4, CURLOPT_CONNECTTIMEOUT, self::$connectTimeout); curl_setopt($spee7fa4, CURLOPT_TIMEOUT, self::$socketTimeout); curl_setopt($spee7fa4, CURLOPT_RETURNTRANSFER, 1); $spf588a3 = curl_exec($spee7fa4); if (curl_errno($spee7fa4)) { $spff6986 = sprintf('curl[%s] error[%s]', $sp8042f4, curl_errno($spee7fa4) . ':' . curl_error($spee7fa4)); $this->triggerError($spff6986); } curl_close($spee7fa4); } else { $spe9ac48 = array('http' => array('method' => 'GET', 'timeout' => self::$connectTimeout + self::$socketTimeout)); $sp98d92c = stream_context_create($spe9ac48); $spf588a3 = file_get_contents($sp8042f4, false, $sp98d92c); } return $spf588a3; } private function post_request($sp8042f4, $sp84fab5 = '') { if (!$sp84fab5) { return false; } $spf588a3 = http_build_query($sp84fab5); if (function_exists('curl_exec')) { $spee7fa4 = curl_init(); curl_setopt($spee7fa4, CURLOPT_URL, $sp8042f4); curl_setopt($spee7fa4, CURLOPT_RETURNTRANSFER, 1); curl_setopt($spee7fa4, CURLOPT_CONNECTTIMEOUT, self::$connectTimeout); curl_setopt($spee7fa4, CURLOPT_TIMEOUT, self::$socketTimeout); if (!$sp84fab5) { curl_setopt($spee7fa4, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); } else { curl_setopt($spee7fa4, CURLOPT_POST, 1); curl_setopt($spee7fa4, CURLOPT_POSTFIELDS, $spf588a3); } $spf588a3 = curl_exec($spee7fa4); if (curl_errno($spee7fa4)) { $spff6986 = sprintf('curl[%s] error[%s]', $sp8042f4, curl_errno($spee7fa4) . ':' . curl_error($spee7fa4)); $this->triggerError($spff6986); } curl_close($spee7fa4); } else { if ($sp84fab5) { $spe9ac48 = array('http' => array('method' => 'POST', 'header' => 'Content-type: application/x-www-form-urlencoded
' . 'Content-Length: ' . strlen($spf588a3) . '
', 'content' => $spf588a3, 'timeout' => self::$connectTimeout + self::$socketTimeout)); $sp98d92c = stream_context_create($spe9ac48); $spf588a3 = file_get_contents($sp8042f4, false, $sp98d92c); } } return $spf588a3; } private function decode_response($sp9cf920, $spcd539f) { if (strlen($spcd539f) > 100) { return 0; } $spa3d2cf = array(); $sp4af303 = array(); $spde5da4 = array('0' => 1, '1' => 2, '2' => 5, '3' => 10, '4' => 50); $sp1ca412 = 0; $spa35002 = 0; $sp84a444 = str_split($sp9cf920); $spde31aa = str_split($spcd539f); for ($sp51a993 = 0; $sp51a993 < strlen($sp9cf920); $sp51a993++) { $sp3f5c2c = $sp84a444[$sp51a993]; if (in_array($sp3f5c2c, $sp4af303)) { continue; } else { $sp9d1f75 = $spde5da4[$sp1ca412 % 5]; array_push($sp4af303, $sp3f5c2c); $sp1ca412++; $spa3d2cf[$sp3f5c2c] = $sp9d1f75; } } for ($sp2c3bec = 0; $sp2c3bec < strlen($spcd539f); $sp2c3bec++) { $spa35002 += $spa3d2cf[$spde31aa[$sp2c3bec]]; } $spa35002 = $spa35002 - $this->decodeRandBase($sp9cf920); return $spa35002; } private function get_x_pos_from_str($spec5cbe) { if (strlen($spec5cbe) != 5) { return 0; } $sp58232f = 0; $spe5569c = 200; $sp58232f = base_convert($spec5cbe, 16, 10); $sp11ac9a = $sp58232f % $spe5569c; $sp11ac9a = $sp11ac9a < 40 ? 40 : $sp11ac9a; return $sp11ac9a; } private function get_failback_pic_ans($sp4749ba, $spf897de) { $spafdcd3 = substr(md5($sp4749ba), 0, 9); $spcc3c54 = substr(md5($spf897de), 10, 9); $spb22506 = ''; for ($sp51a993 = 0; $sp51a993 < 9; $sp51a993++) { if ($sp51a993 % 2 == 0) { $spb22506 = $spb22506 . $spafdcd3[$sp51a993]; } elseif ($sp51a993 % 2 == 1) { $spb22506 = $spb22506 . $spcc3c54[$sp51a993]; } } $sp8cbaa5 = substr($spb22506, 4, 5); $spb611a3 = $this->get_x_pos_from_str($sp8cbaa5); return $spb611a3; } private function decodeRandBase($sp9cf920) { $sp06ca4e = substr($sp9cf920, 32, 2); $sp23b1fd = array(); for ($sp51a993 = 0; $sp51a993 < strlen($sp06ca4e); $sp51a993++) { $sp86cf4c = ord($sp06ca4e[$sp51a993]); $sp11ac9a = $sp86cf4c > 57 ? $sp86cf4c - 87 : $sp86cf4c - 48; array_push($sp23b1fd, $sp11ac9a); } $sp84b27f = $sp23b1fd['0'] * 36 + $sp23b1fd['1']; return $sp84b27f; } private function triggerError($spff6986) { } }