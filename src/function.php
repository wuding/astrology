<?php
/**
 * null合并运算符
 * 
 * http://php.net/manual/zh/migration70.new-features.php
 *
 * @param      array|object   $arr    数组对象
 * @param      string         $key    键名
 * @param      mixed          $value  默认值
 *
 * @return     mixed                  运算结果
 */
function _isset($arr, $key = '', $value = '')
{
	if (is_object($arr)) {
		$arr = (array) $arr;
	}

	// 大于等于 7.0
	if (version_compare(phpversion(), '7.0.0', '>=')) {
		return eval("\$result = \$arr[\$key] ?? \$value;");
		print_r($result);exit;
	}

	// 低版本
	return isset($arr[$key]) ? $arr[$key] : $value;
}
