<?php
namespace App\Library; class Response { public static function json($spf588a3 = array(), $sp6002b5 = 200, array $spdcbcbb = array(), $sp01d909 = 0) { return response()->json($spf588a3, $sp6002b5, $spdcbcbb, $sp01d909); } public static function success($spf588a3 = array()) { return self::json(array('message' => 'success', 'data' => $spf588a3)); } public static function fail($spfd1ead = 'fail', $spf588a3 = array()) { return self::json(array('message' => $spfd1ead, 'data' => $spf588a3), 500); } public static function forbidden($spfd1ead = 'forbidden', $spf588a3 = array()) { return self::json(array('message' => $spfd1ead, 'data' => $spf588a3), 403); } }