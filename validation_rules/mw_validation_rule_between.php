<?php
/**
 * Name: MW Validation Rule Between
 * URI: http://2inc.org
 * Description: 値の文字数が範囲内
 * Version: 1.0.0
 * Author: Takashi Kitajima
 * Author URI: http://2inc.org
 * Created : July 21, 2014
 * Modified:
 * License: GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
class MW_Validation_Rule_Between extends mw_validation_rule {

	/**
	 * バリデーションルール名を指定
	 */
	protected $name = 'between';

	/**
	 * rule
	 * @param mw_wp_form_data $Data
	 * @param string $key name属性
	 * @param array $option
	 * @return string エラーメッセージ
	 */
	public function rule( mw_wp_form_data $Data, $key, $options = array() ) {
		$value = $Data->get( $key );
		if ( !is_null( $value ) && !$this->isEmpty( $value ) ) {
			$defaults = array(
				'min' => 0,
				'max' => 0,
				'message' => __( 'The number of characters is invalid.', MWF_Config::DOMAIN )
			);
			$options = array_merge( $defaults, $options );
			$length = mb_strlen( $value, $this->ENCODE );
			if ( MWF_Functions::is_numeric( $options['min'] ) ) {
				if ( MWF_Functions::is_numeric( $options['max'] ) ) {
					if ( !( $options['min'] <= $length && $length <= $options['max'] ) ) {
						return $options['message'];
					}
				} else {
					if ( $options['min'] > $length ) {
						return $options['message'];
					}
				}
			} elseif ( MWF_Functions::is_numeric( $options['max'] ) ) {
				if ( $options['max'] < $length ) {
					return $options['message'];
				}
			}
		}
	}

	/**
	 * admin
	 * @param numeric $key バリデーションルールセットの識別番号
	 * @param array $value バリデーションルールセットの内容
	 */
	public function admin( $key, $value ) {
		?>
		<table>
			<tr>
				<td><?php esc_html_e( 'The range of the number of characters', MWF_Config::DOMAIN ); ?></td>
				<td>
					<input type="text" value="<?php echo esc_attr( @$value[$this->name]['min'] ); ?>" size="3" name="<?php echo MWF_Config::NAME; ?>[validation][<?php echo $key; ?>][<?php echo esc_attr( $this->name ); ?>][min]" />
					〜
					<input type="text" value="<?php echo esc_attr( @$value[$this->name]['max'] ); ?>" size="3" name="<?php echo MWF_Config::NAME; ?>[validation][<?php echo $key; ?>][<?php echo esc_attr( $this->name ); ?>][max]" />
				</td>
			</tr>
		</table>
		<?php
	}
}