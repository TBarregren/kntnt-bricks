<?php

namespace Kntnt\Bricks;

trait Logger {

	public static final function stringify( $val ) {
		if ( is_null( $val ) ) {
			$out = 'NULL';
		} elseif ( is_bool( $val ) ) {
			$out = $val ? 'TRUE' : 'FALSE';
		} elseif ( is_array( $val ) || is_object( $val ) ) {
			$out = print_r( $val, true );
		} else {
			$out = (string) $val;
		}
		return $out;
	}

	// If `$message` isn't a string, its value is printed. If `$message` is
	// a string, it is written with each occurrence of '%s' replaced with
	// the value of the corresponding additional argument converted to string.
	// Any percent sign that should be written must be escaped with another
	// percent sign, that is `%%`. The message is prefixed with [$context]
	// followed by […] where … is the qualified name of the function calling.
	public static function info( $message = '', ...$args ) {
		return self::_log( 'INFO', $message, $args );
	}

	// If `$message` isn't a string, its value is printed. If `$message` is
	// a string, it is written with each occurrence of '%s' replaced with
	// the value of the corresponding additional argument converted to string.
	// Any percent sign that should be written must be escaped with another
	// percent sign, that is `%%`. The message is prefixed with [$context]
	// followed by […] where … is the qualified name of the function calling.
	public static function error( $message = '', ...$args ) {
		return self::_log( 'ERROR', $message, $args );
	}

	// If `$message` isn't a string, its value is printed. If `$message` is
	// a string, it is written with each occurrence of '%s' replaced with
	// the value of the corresponding additional argument converted to string.
	// Any percent sign that should be written must be escaped with another
	// percent sign, that is `%%`. The message is prefixed with [$context]
	// followed by […] where … is the qualified name of the function calling.
	public static function debug( $message = '', ...$args ) {
		if ( self::is_debugging() ) {
			return self::_log( 'DEBUG', $message, $args );
		}
	}

	// If `$message` isn't a string, its value is printed. If `$message` is
	// a string, it is written with each occurrence of '%s' replaced with
	// the value of the corresponding additional argument converted to string.
	// Any percent sign that should be written must be escaped with another
	// percent sign, that is `%%`. The message is prefixed with [$context]
	// followed by […] where … is the qualified name of the function calling.
	public static function log( $context, $message = '', ...$args ) {
		if ( in_array( strtoupper( $context ), [ 'INFO', 'ERROR' ] ) || self::is_debugging() ) {
			return self::_log( $context, $message, $args );
		}
	}

	// If `$message` isn't a string, its value is printed. If `$message` is
	// a string, it is written with each occurrence of '%s' replaced with
	// the value of the corresponding additional argument converted to string.
	// Any percent sign that should be written must be escaped with another
	// percent sign, that is `%%`. The message is prefixed with [$context]
	// followed by […] where … is the qualified name of the function calling.
	public static final function trace( $message = '', ...$args ) {
		self::_log( 'TRACE', $message, $args, false );
	}

	private static function _log( $context, $message, $args, $single = true ) {
		$trace = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, $single ? 3 : 0 );
		if ( ! is_string( $message ) ) {
			$args    = [ $message ];
			$message = '%s';
		}
		$message = sprintf( $message, ...array_map( [ Theme::class, 'stringify' ], $args ) );
		self::_log_echo( $trace, $context, 1, $single, $message );
		if ( ! $single ) {
			for ( $n = 2; $n < count( $trace ) - 1; ++ $n ) {
				self::_log_echo( $trace, $context, $n, $single, $message );
			}
		}
		return [
			'context' => $context,
			'message' => $message,
		];
	}

	private static function _log_echo( $steps, $context, $n, $single, $message = '' ) {
		$caller = $steps[ $n + 1 ]['function'];
		if ( isset( $steps[ $n + 1 ]['class'] ) ) {
			$caller = $steps[ $n + 1 ]['class'] . $steps[ $n + 1 ]['type'] . $caller;
		}
		$out = "[$context]" . ( $single ? '' : "[#$n]" ) . "[$caller]";
		if ( 1 == $n ) {
			$out = "$out $message";
		}
		error_log( $out );
	}

}
