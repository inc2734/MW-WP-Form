<?php
/**
 * Name       : MW WP Form Error
 * Version    : 1.1.0
 * Author     : Takashi Kitajima
 * Author URI : http://2inc.org
 * Created    : July 17, 2012
 * Modified   : December 31, 2014
 * License    : GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
class MW_WP_Form_Error {

	/**
	 * $errors
	 * エラー格納用の配列
	 * @var array [key => rule = message]
	 */
	protected $errors = array();

	/**
	 * set_error
	 * エラーメッセージをセット
	 * @param string $key name属性
	 * @param string $rule
	 * @param string $message
	 */
	public function set_error( $key, $rule, $message ) {
		if ( !is_string( $message ) ) exit( 'The Validate error message must be string!');
		$this->errors[$key][$rule] = $message;
	}

	/**
	 * get_error
	 * エラーメッセージを返す
	 * @param string $key name属性
	 * @return array
	 */
	public function get_error( $key ) {
		if ( isset( $this->errors[$key] ) ) {
			return $this->errors[$key];
		}
		return array();
	}

	/**
	 * get_errors
	 * 全てのエラーメッセージを返す
	 * @return array
	 */
	public function get_errors() {
		return $this->errors;
	}
}