<?php
class MW_WP_Form_Validation_Rule_Url_Test extends WP_UnitTestCase {

	public function tearDown() {
		parent::tearDown();
		_delete_all_data();
	}

	protected function _create_form() {
		return $this->factory->post->create(
			array(
				'post_type' => MWF_Config::NAME,
			)
		);
	}

	/**
	 * @test
	 * @group rule
	 */
	public function rule() {
		$form_id  = $this->_create_form();
		$form_key = MWF_Functions::get_form_key_from_form_id( $form_id );
		$Data     = MW_WP_Form_Data::connect( $form_key );

		$Rule = new MW_WP_Form_Validation_Rule_Url();
		$Rule->set_Data( $Data );

		$Data->set( 'url', '' );
		$this->assertNull( $Rule->rule( 'url' ) );

		$Data->set( 'url', 'http://example.com' );
		$this->assertNull( $Rule->rule( 'url' ) );
		$Data->set( 'url', 'https://example.com' );
		$this->assertNull( $Rule->rule( 'url' ) );
		$Data->set( 'url', 'https://www.e' );
		$this->assertNull( $Rule->rule( 'url' ) );

		$Data->set( 'url', 'htt://example.com' );
		$this->assertNotNull( $Rule->rule( 'url' ) );
		$Data->set( 'url', 'http:/example.com' );
		$this->assertNotNull( $Rule->rule( 'url' ) );
		$Data->set( 'url', 'https://www.' );
		$this->assertNotNull( $Rule->rule( 'url' ) );
	}
}
