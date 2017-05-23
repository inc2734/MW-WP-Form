<?php
/**
 * Name       : MW WP Form Validation
 * Description: 与えられたデータに対してバリデーションエラーがあるかチェックする
 * Version    : 1.8.5
 * Author     : Takashi Kitajima
 * Author URI : http://2inc.org
 * Created    : July 20, 2012
 * Modified   : April 15, 2015
 * License    : GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
class MW_WP_Form_Validation {

	/**
	 * @var string
	 */
	protected $form_key;

	/**
	 * @var MW_WP_Form_Data
	 */
	protected $Data;

	/**
	 * @var MW_WP_Form_Setting
	 */
	protected $Setting;

	/**
	 * バリデートをかける項目（name属性）と、それにかけるバリデーションの配列
	 * @var array
	 */
	protected $validate = array();

	/**
	 * @param string $form_key
	 */
	public function __construct( $form_key ) {
		$this->form_key = $form_key;
		$this->Data     = MW_WP_Form_Data::connect( $form_key );
		$form_id        = MWF_Functions::get_form_id_from_form_key( $form_key );
		$this->Setting  = new MW_WP_Form_Setting( $form_id );

		$this->_set_rules();
	}

	/**
	 * Set validation rules of this form
	 */
	protected function _set_rules() {
		$validations = $this->Setting->get( 'validation' );
		if ( $validations ) {
			foreach ( $validations as $validation ) {
				foreach ( $validation as $rule => $options ) {
					if ( $rule == 'target' ) {
						continue;
					}
					if ( ! is_array( $options ) ) {
						$options = array();
					}
					$this->set_rule( $validation['target'], $rule, $options );
				}
			}
		}

		$Akismet = new MW_WP_Form_Akismet();
		$akismet_check = $Akismet->is_valid(
			$this->Setting->get( 'akismet_author' ),
			$this->Setting->get( 'akismet_author_email' ),
			$this->Setting->get( 'akismet_author_url' ),
			$this->Data
		);
		if ( $akismet_check ) {
			$this->set_rule( MWF_Config::AKISMET, 'akismet_check' );
		}

		$Validation = apply_filters(
			'mwform_validation_' . $this->form_key,
			$this,
			$this->Data->gets(),
			clone $this->Data
		);
	}

	/**
	 * Set validation rule of the form field.
	 *
	 * @param string $key
	 * @param string $rule
	 * @param array $options
	 * @return MW_WP_Form_Validation
	 */
	public function set_rule( $key, $rule, array $options = array() ) {
		$rules = array(
			'rule'    => strtolower( $rule ),
			'options' => $options
		);
		$this->validate[ $key ][] = $rules;
		return $this;
	}

	/**
	 * Validation check form fields
	 *
	 * @return bool Return true when nothing errors
	 */
	public function check() {
		foreach ( $this->validate as $key => $rules ) {
			$this->_check( $key, $rules );
		}

		return (bool) ! $this->Data->get_validation_errors();
	}

	/**
	 * Validation check the one form field
	 *
	 * @param string $key
	 * @return bool Return true when nothing errors
	 */
	public function single_check( $key ) {
		$rules = array();

		if ( is_array( $this->validate ) && isset( $this->validate[ $key ] ) ) {
			$rules = $this->validate[ $key ];
			$this->_check( $key, $rules );
		}

		return (bool) ! $this->Data->get_validation_error( $key );
	}

	/**
	 * Set varidation errors into MW_WP_Form_Data
	 *
	 * @param string $key
	 * @param array $rules
	 */
	protected function _check( $key, array $rules ) {
		$Validation_Rules = MW_WP_Form_Validation_Rules::instantiation();
		$validation_rules = $Validation_Rules->get_validation_rules();

		foreach ( $rules as $rule_set ) {
			if ( ! isset( $rule_set['rule'] ) ) {
				continue;
			}

			$options = array();
			if ( isset( $rule_set['options'] ) ) {
				$options = $rule_set['options'];
			}

			$rule = $rule_set['rule'];
			if ( ! isset( $validation_rules[ $rule ] ) ) {
				continue;
			}

			$validation_rule = $validation_rules[ $rule ];
			if ( ! is_callable( array( $validation_rule, 'rule' ) ) ) {
				continue;
			}

			$message = $validation_rule->rule( $key, $options );
			if ( empty( $message ) ) {
				continue;
			}

			$this->Data->set_validation_error( $key, $rule, $message );
		}
	}
}
