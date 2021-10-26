<?php

add_filter( 'kntnt-bricks-breakpoints', function ( $breakpoints ) {
	return [
		'tablet_portrait'  => 1023,
		'mobile_landscape' => 767,
		'mobile_portrait'  => 479,
	];
} );