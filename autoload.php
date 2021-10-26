<?php

spl_autoload_register( function ( $class ) {
	static $ns, $ns_len;
	if ( ! $ns ) {
		$dir = strtr( ucwords( basename( __DIR__ ), '-' ), '-', '_' );
		$pos = strpos( $dir, '_', 0 );
		$ns = substr( $dir, 0, $pos ) . '\\' . substr( $dir, $pos + 1 );
		$ns_len = strlen( $ns );
	}
	if ( ( $len = strrpos( $class, '\\' ) ) == $ns_len && substr( $class, 0, $len ) == $ns ) {
		require_once __DIR__ . '/classes/' . substr( $class, $ns_len + 1 ) . '.php';
	}
} );
