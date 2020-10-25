<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return apply_filters( 'wc_enterprise_settings',
	array(
		'enabled' => array(
			'title'       => __( 'Enable/Disable', 'woocommerce-rapid-payment-checkout' ),
			'label'       => __( 'Enable Rapid Payment Checkout', 'woocommerce-rapid-payment-checkout' ),
			'type'        => 'checkbox',
			'description' => '',
			'default'     => 'no'
		),
		'title' => array(
			'title'       => __( 'Title', 'woocommerce-rapid-payment-checkout' ),
			'type'        => 'text',
			'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce-rapid-payment-checkout' ),
			'default'     => __( 'Rapid Payment Checkout', 'woocommerce-rapid-payment-checkout' ),
			'desc_tip'    => true,
		),
		'description' => array(
			'title'       => __( 'Description', 'woocommerce-rapid-payment-checkout' ),
			'type'        => 'text',
			'description' => __( 'This controls the description which the user sees during checkout.', 'woocommerce-rapid-payment-checkout' ),
			'default'     => __( 'Pay using your credit card details.', 'woocommerce-rapid-payment-checkout'),
			'desc_tip'    => true,
		),
		'username' => array(
			'title'       => __( 'API Username', 'woocommerce-rapid-payment-checkout' ),
			'type'        => 'text',
			'description' => __( 'Get your API username from your Rapid Payment Checkout account.', 'woocommerce-rapid-payment-checkout' ),
			'default'     => '',
			'desc_tip'    => true,
		),
        'password' => array(
            'title'       => __( 'API Password', 'woocommerce-rapid-payment-checkout' ),
            'type'        => 'password',
            'description' => __( 'Get your API password from your Rapid Payment Checkout account.', 'woocommerce-rapid-payment-checkout' ),
            'default'     => '',
            'desc_tip'    => true,
        ),
		'logging' => array(
			'title'       => __( 'Logging', 'woocommerce-rapid-payment-checkout' ),
			'label'       => __( 'Log debug messages', 'woocommerce-rapid-payment-checkout' ),
			'type'        => 'checkbox',
			'description' => __( 'Save debug messages to the WooCommerce System Status log.', 'woocommerce-rapid-payment-checkout' ),
			'default'     => 'no',
			'desc_tip'    => true,
		),
	)
);
