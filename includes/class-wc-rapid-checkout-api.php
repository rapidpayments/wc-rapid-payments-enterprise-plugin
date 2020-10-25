<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Rapid_Checkout_API class.
 *
 * Communicates with Rapid Payment Checkout API.
 */
class WC_Rapid_Checkout_API {

	/**
	 * Secret API Username.
	 * @var string
	 */
	private static $username = '';

    /**
     * Secret API Password.
     * @var string
     */
    private static $password = '';

    private static $settings = [
        'api_endpoint' => 'https://secure.rapidpayments.africa/api/v1/',
        'app_domain' => 'https://secure.rapidpayments.africa'
    ];

    /**
     * @param $setting
     * @return mixed|null
     */
    public static function getSetting($setting) {
        $settings = self::$settings;
        if ( file_exists( dirname( __FILE__ ) . '/local-config.php' ) ) {
            $settings = array_merge($settings, require(dirname( __FILE__ ) . '/local-config.php'));
        }

        if (isset($settings[$setting])) {
            return $settings[$setting];
        }
        return null;
    }

	/**
	 * Set api username.
	 * @param string $username
	 */
	public static function set_username( $username ) {
		self::$username = $username;
	}

	/**
	 * Get secret key.
	 * @return string
	 */
	public static function get_username() {
		if ( ! self::$username ) {
			$options = get_option( 'woocommerce_rapid_checkout_settings' );

			if ( isset($options['username'])) {
				self::set_username( $options['username']  );
			}
		}
		return self::$username;
	}

    /**
     * Set api password.
     * @param string $password
     */
    public static function set_password( $password ) {
        self::$password = $password;
    }

    /**
     * Get api password.
     * @return string
     */
    public static function get_password() {
        if ( ! self::$password ) {
            $options = get_option( 'woocommerce_rapid_checkout_settings' );

            if ( isset($options['password'])) {
                self::set_password( $options['password']  );
            }
        }
        return self::$password;
    }

	/**
	 * Send the request to Rapid Payment Checkout's API
	 *
	 * @param mixed $request
	 * @param string $api
	 * @param $method
	 * @return array|WP_Error
	 */
	public static function request( $request, $api = 'user/login', $method = 'POST' ) {
        $endPoint = self::getSetting('api_endpoint') ;
	    $url = $endPoint . $api;
		WC_Rapid_Checkout::log( "{$api} request to ".$url . print_r( $request, true ) );

		$response = wp_remote_post(
            $url,
			array(
				'method'        => $method,
				'headers'       => array(
					'Authorization'  => 'Basic ' . base64_encode( self::get_username(). ':' . self::get_password() ),
				),
				'body'       => apply_filters( 'woocommerce_rapid_checkout_request_body', $request, $api ),
				'timeout'    => 70,
				'user-agent' => 'WooCommerce ' . WC()->version,
                'x-domain' => home_url( '/' )
			)
		);

        if (is_wp_error($response)) {
            WC_Rapid_Checkout::log( "WP Error: ".$response->get_error_message());
            return $response;
        }

        WC_Rapid_Checkout::log( "Response: ".json_encode($response));

        $parsed_response = json_decode( $response['body'] );

        if(empty( $response['body'] ) ) {
            WC_Rapid_Checkout::log( "Error Response: " . print_r( $response, true ) );
            return new WP_Error( 'rapid_checkout_error', __( 'There was a problem connecting to the payment gateway.'.$parsed_response->name, 'woocommerce-rapid-payment-checkout' ) );
        }

        if ( is_wp_error( $response )) {
			WC_Rapid_Checkout::log( "Error Response: " . print_r( $response, true ) );
			return new WP_Error( 'rapid_checkout_error', __( 'There was a problem connecting to the payment gateway.'.$parsed_response->name, 'woocommerce-rapid-payment-checkout' ) );
		}

		// Handle response
        return $parsed_response;
	}

    /**
     * Fetches an api token
     *
     * @return mixed
     * @throws Exception
     */
	public static function get_token_data() {
        $response = self::request( '', 'token', 'POST' );
        if ( is_wp_error( $response ) ) {
            WC_Rapid_Checkout::log( 'Rapid Checkout Token API Error: '.$response->get_error_message() );
            throw new Exception('Rapid Checkout Token: '.$response->get_error_message());
        }
        return $response;
    }

    /**
     * @param $transaction_id
     * @return stdClass|WP_Error
     * @throws Exception
     */
    public static function get_transaction_data($transaction_id) {
        $response = self::request('', 'gateway-transaction/'.$transaction_id, 'GET');
        if ( is_wp_error( $response ) ) {
            WC_Rapid_Checkout::log( 'Rapid Checkout Transaction API Error: '.$response->get_error_message() );
        }
        return $response;
    }

    /**
     * Fetches a payment key
     *
     * @param $data array
     * @return stdClass|WP_Error
     * @throws Exception
     */
    public static function get_payment_key_data($data = []) {
        $data['payment_type'] = [
            'eft',
            'credit_card'
        ];
        $query = http_build_query($data);
        WC_Rapid_Checkout::log(json_encode($data));
        $response = self::request( $query, 'eft/payment-key', 'POST' );
        if ( is_wp_error( $response ) ) {
            WC_Rapid_Checkout::log( 'Rapid Checkout Payment Key API Error: '.$response->get_error_message() );
            throw new Exception('Rapid Checkout Payment Key: '.$response->get_error_message());
        }
        return $response;
    }
}
