<?php
/**
 * Plugin name: „LP Express pastomatai“
 * Plugin URI: https://www.tavoweb.lt/lpexpress-pastomatai/
 * Description: „LP EXPRESS“ siuntų terminalų įskiepis (plugin) skirtas WordPress turinio valdymo sistemai ir WooCommerce įskiepiui. Šio įskiepio pagalba elektroninės parduotuvės pirkėjas gali nurodyti kuriame „LP EXPRESS“ siuntų terminale norės atsiimti prekę (-es).
 * Version: 1.1
 * Author: TavoWEB.lt
 * Author URI: https://tavoweb.lt
 * License: GPLv3
 * Text Domain: woocommerce-lpexpress-pa
 * Paštomatų sarašas (importas negalimas, suvedinėti rankiniu būdu): https://www.lpexpress.lt/lt/Viet-s-ra-as.html#KURRASTI
 */


if ( ! class_exists ( 'WC_lpexpress' ) ) :

/**
 * Pagrindinė įskiepio klasė
 */
class WC_lpexpress {
 public static $instance;

  public static function init() {
    if ( is_null( self::$instance ) ) {
      self::$instance = new WC_lpexpress();
    }
    return self::$instance;
  }
  private function __construct() {
    add_action( 'plugins_loaded', array( $this, 'load_our_textdomain' ) );
    add_action( 'woocommerce_shipping_init', array( $this, 'shipping_method_init' ) );

    add_action( 'woocommerce_before_order_notes', array( $this, 'dropdown' ), 1, 1 );
    add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'update_order_meta' ) );
    add_action( 'woocommerce_admin_order_data_after_billing_address', array( $this, 'display_admin_order_meta' ), 10, 1 );
    add_action( 'woocommerce_checkout_process', array( $this, 'checkout_field_process' ) );
  }

  /**
   * Užkrauti įskipeio informaciją
   */
  public static function load_our_textdomain() {
    load_plugin_textdomain( 'woocommerce-lpexpress', false, dirname( plugin_basename(__FILE__) ) . '/lang/' );
  }

  /**
   * Užkrauti siuntimo būdą
   */
  public static function shipping_method_init() {
    require_once 'inc/class-wc-lpexpress-shipping-method.php'; // WC_lpexpress_Shipping_Method
    add_filter( 'woocommerce_shipping_methods', __CLASS__ . '::add_shipping_method' ); 
	
  }
   
  /**
   * Pridėti siuntimo būdą į WooCommerce
   */
  public static function add_shipping_method( $methods ) {
    $methods[] = 'WC_lpexpress_Shipping_Method';
    return $methods;
  }

  /**
   * Siuntų terminalų pasirinkimas
   */
  public static function dropdown( $checkout ) {
?>
<div id="lpexpress_field">
<h3 id="order_review_heading" >Pristatymo informacija</h3>
<p class="form-row form-row-wide validate-required woocommerce-validated" style="margin-bottom:100px">
  <label for="lpexpress_terminal" class=""><?php _e( 'Pasirinkite artimiausią LP EXPRESS siuntų terminalą', 'woocommerce-lpexpress' ); ?> <abbr class="required" title="<?php _e( 'required', 'woocommerce' ); ?>">*</abbr></label>
  <select name="lpexpress_terminal" id="lpexpress_terminal">
    <option value=""><?php _e( 'Pasirinkite siuntų terminalą', 'woocommerce-lpexpress'); ?></option>
    <option value="Vilnius, Gedimino pr. 7 (Centrinis paštas)">Vilnius, Gedimino pr. 7 (Centrinis paštas)</option>
	<option value="Vilnius, Liepkalnio g. 112 (Maxima XX)">Vilnius, Liepkalnio g. 112 (Maxima XX)</option>
	<option value="Vilnius, Ozo g. 25 (Akropolis)">Vilnius, Ozo g. 25 (Akropolis)</option>
	<option value="Vilnius, P. Lukšio g. 34 (Banginis)">Vilnius, P. Lukšio g. 34 (Banginis)</option>
	<option value="Vilnius, Ukmergės g. 282 (Maxima XXX)">Vilnius, Ukmergės g. 282 (Maxima XXX)</option>
	<option value="Kaunas, A. Juozapavičiaus pr. 84A (NORFA XXL)">Kaunas, A. Juozapavičiaus pr. 84A (NORFA XXL)</option>
	<option value="Kaunas, Islandijos pl. 32 (MEGA)">Kaunas, Islandijos pl. 32 (MEGA)</option>
	<option value="Kaunas, Karaliaus Mindaugo pr. 49 (Akropolis)">Kaunas, Karaliaus Mindaugo pr. 49 (Akropolis)</option>
	<option value="Kaunas, Pramonės pr. 6 / Draugystės g. 8 (Banginis)">Kaunas, Pramonės pr. 6 / Draugystės g. 8 (Banginis)</option>
	<option value="Kaunas, Raudondvario pl. 284A (Maxima XX)">Kaunas, Raudondvario pl. 284A (Maxima XX)</option>
	<option value="Kaunas, Savanorių pr. 255 (Maxima XXX)">Kaunas, Savanorių pr. 255 (Maxima XXX)</option>
	<option value="Kaunas, Veiverių g. 150B (Maxima Bazė)">Kaunas, Veiverių g. 150B (Maxima Bazė)</option>
	<option value="Klaipėda, Liepojos g. 10 (Maxima XX)">Klaipėda, Liepojos g. 10 (Maxima XX)</option>
	<option value="Klaipėda, Šilutės pl. 35 (Banginis)">Klaipėda, Šilutės pl. 35 (Banginis)</option>
	<option value="Klaipėda, Taikos pr. 61 (Akropolis)">Klaipėda, Taikos pr. 61 (Akropolis)</option>
	<option value="Šiauliai, Aido g. 8 (Akropolis)">Šiauliai, Aido g. 8 (Akropolis)</option>
	<option value="Šiauliai, Tilžės g. 225 (Norfa XL (PC Tilžė))">Šiauliai, Tilžės g. 225 (Norfa XL (PC Tilžė))</option>
	<option value="Panevėžys, Klaipėdos g. 92 (Maxima XX)">Panevėžys, Klaipėdos g. 92 (Maxima XX)</option>
	<option value="Panevėžys, Respublikos g. 71 (Maxima XX)">Panevėžys, Respublikos g. 71 (Maxima XX)</option>
	<option value="Alytus, Naujoji g. 90 (Maxima XX)">Alytus, Naujoji g. 90 (Maxima XX)</option>
	<option value="Alytus, Santaikos g. 34G (Maxima XX)">Alytus, Santaikos g. 34G (Maxima XX)</option>
	<option value="Anykščiai, Žiburio g. 12 (NORFA XL)">Anykščiai, Žiburio g. 12 (NORFA XL)</option>
	<option value="Biržai, Vabalninko g. 8a (Maxima X)">Biržai, Vabalninko g. 8a (Maxima X)</option>
	<option value="Didžioji Riešė, Molėtų g. 13 (IKI Riešė)">Didžioji Riešė, Molėtų g. 13 (IKI Riešė)</option>
	<option value="Druskininkai, M. K. Čiurlionio g. 50 (Aidas)">Druskininkai, M. K. Čiurlionio g. 50 (Aidas)</option>
	<option value="Elektrėnai, Rungos g. 4 (Maxima XX)">Elektrėnai, Rungos g. 4 (Maxima XX)</option>
	<option value="Gargždai, Klaipėdos g. 37 (Maxima XX)">Gargždai, Klaipėdos g. 37 (Maxima XX)</option>
	<option value="Ignalina, Taikos g. 11 (Maxima XX)">Ignalina, Taikos g. 11 (Maxima XX)</option>
	<option value="Jonava, Chemikų g. 2 (NORFA XL)">Jonava, Chemikų g. 2 (NORFA XL)</option>
	<option value="Joniškis, Upytės g. 19 (Maxima X)">Joniškis, Upytės g. 19 (Maxima X)</option>
	<option value="Jurbarkas, Algirdo g. 1A (Maxima X)">Jurbarkas, Algirdo g. 1A (Maxima X)</option>
	<option value="Kaišiadorys, Gedimino g. 110 (Maxima X)">Kaišiadorys, Gedimino g. 110 (Maxima X)</option>
	<option value="Kaunas, Jonavos g. 3 (IKI Lituanica)">Kaunas, Jonavos g. 3 (IKI Lituanica)</option>
	<option value="Kaunas, K. Baršausko g. 57 (Statoil)">Kaunas, K. Baršausko g. 57 (Statoil)</option>
	<option value="Kaunas, K. Petrausko g. 6 (Express Market)">Kaunas, K. Petrausko g. 6 (Express Market)</option>
	<option value="Kaunas, K. Škirpos g. 17 (Šilas)">Kaunas, K. Škirpos g. 17 (Šilas)</option>
	<option value="Kaunas, Pramonės pr. 29 (Maxima XXX)">Kaunas, Pramonės pr. 29 (Maxima XXX)</option>
	<option value="Kaunas, Raudondvario pl. 94B (Rimi)">Kaunas, Raudondvario pl. 94B (Rimi)</option>
	<option value="Kaunas, V. Krėvės pr. 14B (Maxima XX)">Kaunas, V. Krėvės pr. 14B (Maxima XX)</option>
	<option value="Kaunas, V. Krėvės pr. 97 (IKI Saulėtekis)">Kaunas, V. Krėvės pr. 97 (IKI Saulėtekis)</option>
	<option value="Kaunas, Žemaičių pl. 19 (Statoil)">Kaunas, Žemaičių pl. 19 (Statoil)</option>
	<option value="Kėdainiai, J. Basanavičiaus g. 53 (Maxima XX)">Kėdainiai, J. Basanavičiaus g. 53 (Maxima XX)</option>
	<option value="Kelmė, S. Šilingo g. 5 (NORFA XL)">Kelmė, S. Šilingo g. 5 (NORFA XL)</option>
	<option value="Klaipėda, H. Manto g. 11 (Maxima X)">Klaipėda, H. Manto g. 11 (Maxima X)</option>
	<option value="Klaipėda, Sausio 15-osios g. 1A/2A (Statoil)">Klaipėda, Sausio 15-osios g. 1A/2A (Statoil)</option>
	<option value="Klaipėda, Taikos pr. 115 (IKI Žardė)">Klaipėda, Taikos pr. 115 (IKI Žardė)</option>
	<option value="Klaipėda, Taikos pr. 139 (BIG)">Klaipėda, Taikos pr. 139 (BIG)</option>
	<option value="Kretinga, Žemaitės al. 29 (Maxima XX)">Kretinga, Žemaitės al. 29 (Maxima XX)</option>
	<option value="Kupiškis, Gedimino g. 53N (Maxima XX)">Kretinga, Gedimino g. 53N (Maxima XX)</option>
	<option value="Kuršėnai, Vilniaus g. 2 (Norfa)">Kuršėnai, Vilniaus g. 2 (Norfa)</option>
	<option value="Lazdijai, Seinų g. 1 (Viešoji biblioteka)">Lazdijai, Seinų g. 1 (Viešoji biblioteka)</option>
	<option value="Marijampolė, Kauno g. 130 (Statoil)">Marijampolė, Kauno g. 130 (Statoil)</option>
	<option value="Marijampolė, Sporto g. 16 (Maxima X)">Marijampolė, Sporto g. 16 (Maxima X)</option>
	<option value="Marijampolė, V. Kudirkos g. 3 (Maxima XXX)">Marijampolė, V. Kudirkos g. 3 (Maxima XXX)</option>
	<option value="Mažeikiai, Laisvės g. 56 (Maxima XX)">Mažeikiai, Laisvės g. 56 (Maxima XX)</option>
	<option value="Mažeikiai, Žemaitijos g. 20 (Maxima XX)">Mažeikiai, Žemaitijos g. 20 (Maxima XX)</option>
	<option value="Naujoji Akmenė, V. Kudirkos g. 18 (Maxima XX)">Naujoji Akmenė, V. Kudirkos g. 18 (Maxima XX)</option>
	<option value="Palanga, Plytų g. 9a (Maxima X)">Palanga, Plytų g. 9a (Maxima X)</option>
	<option value="Panevėžys, Klaipėdos g. 143A (Babilonas)">Panevėžys, Klaipėdos g. 143A (Babilonas)</option>
	<option value="Panevėžys, Ukmergės g. 18 (IKI Basanavičiaus)">Panevėžys, Ukmergės g. 18 (IKI Basanavičiaus)</option>
	<option value="Pasvalys, Vilniaus g. 48 (Maxima X)">Pasvalys, Vilniaus g. 48 (Maxima X)</option>
	<option value="Plungė, J.Tumo-Vaižganto g. 81 (Maxima XX)">Plungė, J.Tumo-Vaižganto g. 81 (Maxima XX)</option>
	<option value="Prienai, Vytauto g. 17 (Maxima X)">Prienai, Vytauto g. 17 (Maxima X)</option>
	<option value="Radviliškis, Gedimino g. 42A (Maxima XX)">Radviliškis, Gedimino g. 42A (Maxima XX)</option>
	<option value="Raseiniai, Vilniaus g. 93 (Maxima XX)">Raseiniai, Vilniaus g. 93 (Maxima XX)</option>
	<option value="Rokiškis, Respublikos g. 111B (Maxima XX)">Rokiškis, Respublikos g. 111B (Maxima XX)</option>
	<option value="Šakiai, V. Kudirkos g. 66 (Maxima X)">Šakiai, V. Kudirkos g. 66 (Maxima X)</option>
	<option value="Šiauliai, Dubijos g. 20A (Statoil)">Šiauliai, Dubijos g. 20A (Statoil)</option>
	<option value="Šiauliai, Gardino g. 2 (IKI Dainai)">Šiauliai, Gardino g. 2 (IKI Dainai)</option>
	<option value="Šilalė, S. Dariaus ir S. Girėno g. 12 (Maxima X)">Šilalė, S. Dariaus ir S. Girėno g. 12 (Maxima X)</option>
	<option value="Šilutė, Lietuvininkų g. 39 (IKI)">Šilutė, Lietuvininkų g. 39 (IKI)</option>
	<option value="Tauragė, Bernotiškės g. 3 (NORFA XL)">Tauragė, Bernotiškės g. 3 (NORFA XL)</option>
	<option value="Telšiai, Turgaus a. 15A (Swedbank)">Telšiai, Turgaus a. 15A (Swedbank)</option>
	<option value="Ukmergė, Žiedo g. 1 (Maxima XX)">Ukmergė, Žiedo g. 1 (Maxima XX)</option>
	<option value="Utena, Aušros g. 78 (Maxima XX)">Utena, Aušros g. 78 (Maxima XX)</option>
	<option value="Varėna, Vytauto g. 13 (Maxima X)">Varėna, Vytauto g. 13 (Maxima X)</option>
	<option value="Vilkaviškis, J. Basanavičiaus a. 1 (Maxima XX)">Vilkaviškis, J. Basanavičiaus a. 1 (Maxima XX)</option>
	<option value="Vilnius, Baltupio g. 10 (Statoil)">Vilnius, Baltupio g. 10 (Statoil)</option>
	<option value="Vilnius, Fabijoniškių g. 2A (IKI Fabijoniškės)">Vilnius, Fabijoniškių g. 2A (IKI Fabijoniškės)</option>
	<option value="Vilnius, Geležinkelio g. 16 (Geležinkelio stotis)">Vilnius, Geležinkelio g. 16 (Geležinkelio stotis)</option>
	<option value="Vilnius, J. Jasinskio g. 16 (Verslo trikampis)">Vilnius, J. Jasinskio g. 16 (Verslo trikampis)</option>
	<option value="Vilnius, J. Tiškevičiaus g. 22 (Maxima XX)">Vilnius, J. Tiškevičiaus g. 22 (Maxima XX)</option>
	<option value="Vilnius, Laisvės pr. 43C (Statoil)">Vilnius, Laisvės pr. 43C (Statoil)</option>
	<option value="Vilnius, Mindaugo g. 11 (Maxima XXX)">Vilnius, Mindaugo g. 11 (Maxima XXX)</option>
	<option value="Vilnius, Naugarduko g. 84 (Maxima XX)">Vilnius, Naugarduko g. 84 (Maxima XX)</option>
	<option value="Vilnius, Pilaitės pr. 31 (Maxima XX)">Vilnius, Pilaitės pr. 31 (Maxima XX)</option>
	<option value="Vilnius, Saltoniškių g. 9 (Panorama)">Vilnius, Saltoniškių g. 9 (Panorama)</option>
	<option value="Vilnius, Taikos g. 162A (Maxima XX)">Vilnius, Taikos g. 162A (Maxima XX)</option>
	<option value="Vilnius, Tuskulėnų g. 66 (Maxima XX)">Vilnius, Tuskulėnų g. 66 (Maxima XX)</option>
	<option value="Vilnius, Ukmergės g. 369 (BIG)">Vilnius, Ukmergės g. 369 (BIG)</option>
	<option value="Vilnius, Žalgirio g. 105 (Maxima XX)">Vilnius, Žalgirio g. 105 (Maxima XX)</option>
	<option value="Vilnius, Žirmūnų g. 2 (IKI Minskas)">Vilnius, Žirmūnų g. 2 (IKI Minskas)</option>
	<option value="Visaginas, Taikos g. 66 (Maxima X)">Visaginas, Taikos g. 66 (Maxima X)</option>
	<option value="Zarasai, D. Bukonto g. 7 (Maxima X)">Zarasai, D. Bukonto g. 7 (Maxima X)</option>
  </select>
  <small><a target="_blank" href="https://www.lpexpress.lt/lt/Viet-s-ra-as.html">(Terminalų žemėlapis ir adresai)</a></small>
</p>
</div>
<script>
(function($){
$(document).ready(function() {
  // rodyti, jeigu siuntimo būdas pasirinktas lpexpress
  function toggleTerminalSelect() {
    if( $('input.shipping_method:checked').val() === 'lpexpress'
        || $('.shipping_method option:selected').val() === 'lpexpress'
        || $('input[type="hidden"].shipping_method').val() === 'lpexpress') {
      $('#lpexpress_field').show();
    }
    else {
      $('#lpexpress_field').hide();
    }
  }

  $(document).on('change', '#shipping_method input:radio', toggleTerminalSelect);
  $(document).on('change', 'select.shipping_method', toggleTerminalSelect);
  $(document).on('change', 'input.shipping_method', toggleTerminalSelect);
  toggleTerminalSelect();

  // use $.select2 if available
  if( $().select2 ) {
    $('select[name="lpexpress_terminal"]').select2({
      placeholder: "<?php _e( 'Pasirinkite siuntų terminalą', 'woocommerce-lpexpress'); ?>",
      placeholderOption: 'first'
    });
  }
});
})(jQuery);
</script>
<?php
  }

  /**
   * Užpildyti užsakymo informaciją
   *
   * @return void
   */
  function update_order_meta( $order_id ) {
    if ( ! empty( $_POST['lpexpress_terminal'] ) ) {
      update_post_meta( $order_id, 'lpexpress_terminal', sanitize_text_field( $_POST['lpexpress_terminal'] ) );
    }
  }

  /**
   * Galutinis apmokėjimo žingsnis
   *
   * @return void
   *//*
  function checkout_field_process() {
    // Tikrinama ar laukas užpildytas, jeigu ne - klaida
    if ( ! $_POST['lpexpress_terminal'] ) {
      wc_add_notice( __( 'Jūs nepasirinkote paštomato. Prašome pasirinkti <strong>paštomato adresą</strong>.', 'woocommerce-lpexpress' ), 'error' );
    }
  }*/

  /**
   * Rodoma informacija wp-admin užsakyme
   *
   * @return void
   */
  function display_admin_order_meta( $order ){
?>
<p><strong><?php _e( 'Siuntų terminalas:', 'woocommerce-lpexpress' ); ?></strong> <?php echo get_post_meta( $order->id, 'lpexpress_terminal', true ); ?></p>
<?php
  }

}

endif;

// Inicijuojamas įskiepis
$woocommerce_lpexpress = WC_lpexpress::init();
//support widget
require_once 'widget.php';