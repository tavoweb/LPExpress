<?php
/**
 * Filename: class-wc-lpexpress-shipping-method.php
 * Description: Our main shipping method class
 */

if ( ! class_exists( 'WC_lpexpress_Shipping_Method' ) ) :

class WC_lpexpress_Shipping_Method extends WC_Shipping_Method {

  /**
   * Our shipping class constructor
   *
   * @access public
   * @return void
   */
  public function __construct() {
    $this->id = 'lpexpress';
    $this->method_title = __( 'LP EXPRESS-PA', 'woocommerce-lpexpress' );
    $this->method_description = __( 'Siųsti į LP EPXRESS terminalus visoje Lietuvoje', 'woocommerce-lpexpress' );
    $this->init();

		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
  }

  /**
   * Init our plugin settings
   *
   * @access public
   * @return void
   */
  public function init() {
    // Load the settings API
    $this->init_form_fields();
    $this->init_settings();

    // Define settings
		$this->title = $this->get_option( 'title' );
		$this->enabled = $this->get_option( 'enabled' );
		/*$this->availability = $this->get_option( 'availability' );
		$this->countries = $this->get_option( 'countries' );*/
		$this->tax_status = $this->get_option( 'tax_status' );
		$this->cost = $this->get_option( 'cost' );
  }

  /**
  * Initialise Gateway Settings Form Fields
  */
  function init_form_fields() {
    $this->form_fields = array(
      'enabled' => array(
        'title' 		=> __( 'Įjungti/Išjungti', 'woocommerce' ),
        'type' 			=> 'checkbox',
        'label' 		=> __( 'Įjungti šį pristatymo būdą', 'woocommerce' ),
        'default' 		=> 'no',
      ),
      'title' => array(
        'title' 		=> __( 'Būdo pavadinimas', 'woocommerce' ),
        'type' 			=> 'text',
        'description' 	=> __( 'Čia nustatomas pavadinimas, kurį pirkėjas mato atsiskaitydamas', 'woocommerce' ),
        'default'		=> __( 'LP EXPRESS siuntų terminalas', 'woocommerce-lpexpress' ),
        'desc_tip'		=> true
      ),
    /*  'availability' => array(
        'title' 		=> __( 'Availability', 'woocommerce' ),
        'type' 			=> 'select',
        'default' 		=> 'all',
        'class'			=> 'availability wc-enhanced-select',
        'options'		=> array(
          'all' 		=> __( 'Visos šalys', 'woocommerce' ),
          'specific' 	=> __( 'Pasirinktos šalys', 'woocommerce' ),
        ),
      ),
      'countries' => array(
        'title' 		=> __( 'Pasirinktos šalys', 'woocommerce' ),
        'type' 			=> 'multiselect',
        'class'			=> 'wc-enhanced-select',
        'css'			=> 'width: 450px;',
        'default' 		=> '',
        'options'		=> WC()->countries->get_shipping_countries(),
        'custom_attributes' => array(
          'data-placeholder' => __( 'Pasirinkite šalis', 'woocommerce' )
        )
      ),*/
      'tax_status' => array(
        'title' 		=> __( 'Mokesčių būsena', 'woocommerce' ),
        'type' 			=> 'select',
        'class'         => 'wc-enhanced-select',
        'default' 		=> 'taxable',
        'options'		=> array(
          'taxable' 	=> __( 'Apmokestinamas', 'woocommerce' ),
          'none' 		=> _x( 'Nieko', 'Tax status', 'woocommerce' )
        )
      ),
      'cost' => array(
        'title' 		=> __( 'Siuntimo kaina', 'woocommerce' ),
		'class'			=> 'wc_input_price',
		'id'			=> 'cost',
        'type' 			=> 'floate',
        'placeholder'	=> 'Siuntimo kaina',
        'description'	=> __( 'Nustatyti siuntimo kainą pagal parduotuvės valiutą' ),
        'default'		=> '',
        'desc_tip'		=> true
      )/*,
	  'maziausia-kaina' => array(
        'title' 		=> __( 'Mažiausia apsipirkimo suma', 'woocommerce' ),
        'type' 			=> 'number',
        'placeholder'	=> 'Mažiausia leidžiama apsipirkimo suma',
        'description'	=> __( 'Mažiausia leidžiama apsipirkimo suma' ),
        'default'		=> 30,
		'desc_tip'		=> true
      )*/
    );
  }
  

  /**
   * Return our shipping costs
   *
   * @access public
   * @param mixed $package
   * @return void
   */
  public function calculate_shipping( $package ) {
    $rate = array(
      'id' => $this->id,
      'label' => $this->title,
      'cost' => $this->cost,
    );

    // Register the rate
    $this->add_rate( $rate );
  }

	/**
	 * is_available function.
	 *
	 * @param array $package
	 * @return bool
	 */
	public function is_available( $package ) {
		if ( "no" === $this->enabled ) {
			return false;
		}
		if ( 'including' === $this->availability ) {
			if ( is_array( $this->countries ) && ! in_array( $package['destination']['country'], $this->countries ) ) {
				return false;
			}
		} else {
			if ( is_array( $this->countries ) && ( in_array( $package['destination']['country'], $this->countries ) || ! $package['destination']['country'] ) ) {
				return false;
			}
		}
		return apply_filters( 'woocommerce_shipping_' . $this->id . '_is_available', true, $package );
	}
	
	
	
	
}

endif;

