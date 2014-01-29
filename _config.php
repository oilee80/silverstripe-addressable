<?php

switch( SS_ENVIRONMENT_TYPE ) {
	case 'dev':
		if( !defined( 'ADDRESSABLE_CACHE_TIMEOUT' ) )
			define( 'ADDRESSABLE_CACHE_TIMEOUT', 0 );	// No Cacheing
		break;

	case 'test':
		if( !defined( 'ADDRESSABLE_CACHE_TIMEOUT' ) )
			define( 'ADDRESSABLE_CACHE_TIMEOUT', 3600 );	// 1 hour
		break;

	case 'live':
		if( !defined( 'ADDRESSABLE_CACHE_TIMEOUT' ) )
			define( 'ADDRESSABLE_CACHE_TIMEOUT', 86400 );	// 1 minute
		break;
}

define( 'ADDRESSABLE_DIR', basename( dirname( __FILE__ ) ) );