<?php

/**
 * GeoLocation Form Field
 *
 * @todo Add Google Maps jQuery functionality as Requirements::custom_script
 *
 * @package Addressable
 * @author Lee Bradley (bradley.lee80@googlemail.com)
 */
class GeoLocationField extends CompositeField {

	private	$addLine1Field,
			$addLine2Field,
			$townField,
			$cityField,
			$regionField,
			$postalCodeField,
			$countryCodeField,
			$automaticLocationField,
			$latField,
			$lngField;

/**
 * Need to check what there values should be
 */
	const	MIN_LATITUDE = -90,
			MAX_LATITUDE = 90,
			MIN_LONGITUDE = -180,
			MAX_LONGITUDE = 180,
			MIN_MAP_ZOOM = 0,	// Whole World
			MAX_MAP_ZOOM = 19;	// Greatest Detail

	private static	$defaultLatitude = 100,
					$defaultLongitude = -1,
					$defaultZoom = 12;

	/**
	 * Update the default Latitude to use
	 * @param Int $val
	 */
	public static function setDefaultLatitude( $val ) {
		if( !is_int( $val ) )
			throw new Exception( sprintf( 'Invalid Default Latitude: %s is not an integer', $val ) );
		if( $val < static::MIN_LATITUDE || $val < static::MAX_LATITUDE )
			throw new Exception( sprintf( 'Latitude outside of range: %d -> %d', static::MIN_LATITUDE, static::MAX_LATITUDE ) );

		static::$defaultLatitude = $val;
	}
	/**
	 * Update the default Longitude to use
	 * @param Int $val
	 */
	public static function setDefaultLongitude( $val ) {
		if( !is_int( $val ) )
			throw new Exception( sprintf( 'Invalid Default Longitude: %s is not an integer', $val ) );
		if( $val < static::MIN_LONGITUDE || $val < static::MAX_LONGITUDE )
			throw new Exception( sprintf( 'Longitude outside of range: %d -> %d', static::MIN_LONGITUDE, static::MAX_LONGITUDE ) );

		static::$defaultLongitude = $val;
	}

	/**
	 * Constructor for GeoLocationField
	 * @param String $name The Name of the DB Field to be used
	 * @param String $title The Title to be used in the Label for the Form
	 * @param Mixed $values The values to appear in the Fields
	 */
	public function __construct( $name, $title = null, $values = null ) {

		$this->addLine1Field = new TextField( $name . '_AddLine1', 'Address Line 1');
		$this->addLine1Field->addExtraClass( 'geo-location-add-line-1-field' );
		$this->addLine2Field = new TextField( $name . '_AddLine2', 'Address Line 2');
		$this->addLine2Field->addExtraClass( 'geo-location-add-line-2-field' );
		$this->townField = new TextField( $name . '_Town', 'Town');
		$this->townField->addExtraClass( 'geo-location-town-field' );
		$this->cityField = new TextField( $name . '_City', 'City');
		$this->cityField->addExtraClass( 'geo-location-city-field' );
		$this->regionField = new TextField( $name . '_Region', 'Region');
		$this->regionField->addExtraClass( 'geo-location-region-field' );
		$this->postalCodeField = new TextField( $name . '_PostalCode', 'Postal Code');
		$this->postalCodeField->addExtraClass( 'geo-location-postal-code-field' );
		$this->countryCodeField = new CountryDropdownField( $name . '_CountryCode', 'Country Code');
		$this->countryCodeField->addExtraClass( 'geo-location-country-code-field' );
		$this->automaticLocationField = new CheckboxField( $name . '_AutomaticLocation', 'Determine Location Automatically' );
		$this->automaticLocationField->addExtraClass( 'geo-location-automatic-location-field' );
		$this->latField = new NumericField( $name . '_Lat', 'Latitude');
		$this->latField->addExtraClass( 'geo-location-latitude-field' );
		$this->lngField = new NumericField( $name . '_Lng', 'Longitude');
		$this->lngField->addExtraClass( 'geo-location-longitude-field' );
		$mapArea = new CompositeField( array(
			new LiteralField( $name . 'Map', sprintf( '<div class="map_wrapper" id="%s_map">Map should appear here</div>', $name ) ),
			$this->automaticLocationField,
			$this->latField,
			$this->lngField,
		) );
		$mapArea->addExtraClass( 'geo-location-map-area' );
		$fields = new FieldList( array(
			$this->addLine1Field,
			$this->addLine2Field,
			$this->townField,
			$this->cityField,
			$this->regionField,
			$this->postalCodeField,
			$this->countryCodeField,
			$mapArea,
		) );
		$this->setAttribute( 'data-default-lat', static::$defaultLatitude );
		$this->setAttribute( 'data-default-lng', static::$defaultLongitude );
		$this->setAttribute( 'data-default-zoom', static::$defaultZoom );

		parent::__construct( $fields );
		$this->setName( $name );
		$this->setTitle = ($title === null) ? self::name_to_label($name) : $title;

		if($values !== NULL) $this->setValue($values);
	}

	/**
	 * Add the CSS & JS Requirements for the full support of the GeoLocation field
	 */
	private function setRequirements() {

// Will need to include some CSS & JS for the Map to appear
		Requirements::javascript( '//www.google.com/jsapi/' );
		Requirements::javascript( ADDRESSABLE_DIR . '/js/admin_geo_location.js' );

		Requirements::css( ADDRESSABLE_DIR . '/css/admin_geo_location.css' );
	}

	/**
	 * Returns the form field - used by templates.
	 * Although FieldHolder is generally what is inserted into templates, all of the field holder
	 * templates make use of $Field.  It's expected that FieldHolder will give you the "complete"
	 * representation of the field on the form, whereas Field will give you the core editing widget,
	 * such as an input tag.
	 * 
	 * @param array $properties key value pairs of template variables
	 * @return string
	 */
	public function Field($properties = array()) {
		$this->setRequirements();
		return parent::Field( $properties );
	}

	/**
	 * Returns a "field holder" for this field - used by templates.
	 * 
	 * Forms are constructed by concatenating a number of these field holders.
	 * The default field holder is a label and a form field inside a div.
	 * @see FieldHolder.ss
	 * 
	 * @param array $properties key value pairs of template variables
	 * @return string
	 */
	public function FieldHolder($properties = array()) {
		$this->setRequirements();
		return parent::FieldHolder( $properties );
	}

	public function getAddLine1Field() {
		return $this->addLine1Field;
	}

	public function getAddLine2Field() {
		return $this->addLine2Field;
	}

	public function getTownField() {
		return $this->townField;
	}

	public function getCityField() {
		return $this->cityField;
	}

	public function getRegionField() {
		return $this->regionField;
	}

	public function getPostalCodeField() {
		return $this->postalCodeField;
	}

	public function getCountryCodeField() {
		return $this->countryCodeField;
	}

	public function getAutomaticLocationField() {
		return $this->automaticLocationField;
	}

	public function getLatField() {
		return $this->latField;
	}

	public function getLngField() {
		return $this->lngField;
	}

	/**
	 * Set the field value.
	 * 
	 * @param mixed $value
	 * @return FormField Self reference
	 */
	public function setValue( $value ) {
Debug::dump( func_get_args( ) );die();
		return $this;
	}

	public function getValue() {
Debug::dump( func_get_args() );die();
		return 'SOMETHING';
	}

	private static $addressLookupClass = 'GoogleMaps_Address_API';

	public static function setAddressLookupClass( $className ) {
		if( !class_exists( $className ) )
			throw new Exception( sprintf( 'Class doesn`t exist (%s)', $className ) );
		if( !method_exists( $className, 'findByAddress') )
			throw new Exception( sprintf( '%s::findByAddress doesn`t exist', $className ) );
		static::$addressLookupClass = $className;
	}

	/**
	 * @param Validator
	 * @return boolean
	 */
	public function validate( $validator ) {
		$addressArr = array(
			trim( $this->getAddLine1Field()->Value() ),
			trim( $this->getAddLine2Field()->Value() ),
			trim( $this->getTownField()->Value() ),
			trim( $this->getCityField()->Value() ),
			trim( $this->getRegionField()->Value() ),
			trim( $this->getPostalCodeField()->Value() ),
		);

		$addressObj = call_user_func( array( static::$addressLookupClass, 'findByAddress'), $addressArr, $this->getCountryCodeField()->Value() );
		if( is_a( $addressObj, 'Exception' ) ) {
			$validator->validationError( $this->name, $addressObj->getMessage() );
		} else {

		}
// Debug::dump( $addressObj );
		// var_dump( $this->getAddLine1Field()->Value() );
// Debug::dump( $this->getForm()->getRecord()->getField( $this->Name . '_StreetName' ) );die();
// Debug::dump( $validator );die();
// Debug::dump( $this->getRecord() );die();
// Debug::dump( $validator->getForm()->getRecord() );die();

	}
}

interface GeoLocationAddress {

	/**
	 * Find an address using to GoogleMaps API
	 * @param Array $addressArray Each line of the address should be a value in the array
	 * @param String $countryCode Passing in a Country Code
	 * @return Mixed an Exception where the message can be used 
	 */
	public static function findByAddress( $addressArray, $countryCode );

	/**
	 */
	public static function getAddressLine1();

	/**
	 */
	public static function getAddressLine2();

	/**
	 */
	public static function getTown();

	/**
	 */
	public static function getCity();

	/**
	 */
	public static function getRegion();

	/**
	 */
	public static function getPostalCode();

	/**
	 */
	public static function getCountryCode();

	/**
	 */
	public static function getLat();

	/**
	 */
	public static function getLng();
}

/**
 * 
 */
class GoogleMaps_Address_API implements GeoLocationAddress {

	private	$addressLine1,
			$addressLine2,
			$town,
			$city,
			$region,
			$postalCode,
			$countryCode,
			$latitude,
			$longitude;

	/**
	 * API Response Status values
	 */
	const	API_RESPONSE_OK = 'OK',
			API_RESPONSE_ZERO_RESULTS = 'ZERO_RESULTS',
			API_RESPONSE_OVER_QUERY_LIMIT = 'OVER_QUERY_LIMIT',
			API_RESPONSE_REQUEST_DENIED = 'REQUEST_DENIED',
			API_RESPONSE_INVALID_REQUEST = 'INVALID_REQUEST',
			API_RESPONSE_UNKNOWN_ERROR = 'UNKNOWN_ERROR';

	/**
	 * API Server access details
	 */
	const	API_SUB_URL = '/maps/api/geocode/json?',
			API_SERVER = 'http://maps.googleapis.com';

	public function __construct( $data ) {
		$this->loadData( $data );
	}

	/**
	 * Find an address using to GoogleMaps API
	 * @param Array $addressArray Each line of the address should be a value in the array
	 * @param String $countryCode Passing in a Country Code 
	 */
	public static function findByAddress( $addressArray, $countryCode ) {
		$addressArray = array_filter( $addressArray );
		// $req = sprintf( 'http://maps.googleapis.com/maps/api/geocode/json?address=%s&region=%s&sensor=false', urlencode( implode( ',', $addressArray ) ), $countryCode );
		// if( !( $response = @file_get_contents( $req ) ) ) {
		// 	throw new Exception( sprintf( 'Unable to contact (%s)', $req ) );
		// }
		$data = array(
			'address' => implode( ',', $addressArray ),
			'region' => $countryCode,
			'sensor' => 'false'
		);
		$params = array();
		foreach( $data As $param => $paramValue)
			$params[] = urlencode( $param ) . '=' . urlencode( $paramValue );

		$req = new RestfulService( static::API_SERVER, ADDRESSABLE_CACHE_TIMEOUT );
		$response = $req->request( static::API_SUB_URL . implode( '&', $params ) );
		switch( $response->getStatusCode()) {
			case 200:
				if( !( $geoCode = @json_decode( $response->getBody() ) ) ) {
					return new Exception( sprintf( 'Unable to decode Address Data: %s', $response->getBody() ) );
				}
				switch( $geoCode->status ) {
					case static::API_RESPONSE_OK:
						$addressObj = new GoogleMaps_Address_API( $geoCode->results[0] );
						return $addressObj;
						break;
					case static::API_RESPONSE_ZERO_RESULTS:
						return new Exception( sprintf( 'No Results found for: %s', $data[ 'address' ] ) );
					case static::API_RESPONSE_OVER_QUERY_LIMIT:
						return new Exception( 'Addres lookup query limit exceeded' );
					case static::API_RESPONSE_REQUEST_DENIED:
						return new Exception( sprintf( 'Address lookup request denied: %s', $response->getBody() ) );
					case static::API_RESPONSE_INVALID_REQUEST:
						return new Exception( sprintf( 'Invalid address lookup request: %s', $params ) );
					case static::API_RESPONSE_UNKNOWN_ERROR:
						return new Expection( sprintf( 'An unknown error occured during address Lookup: %s', $response->getBody() ) );
				}
				break;
			default:
				return new Exception( sprintf( 'Error retrieing Address Data: "%s"', $response->getBody() ), 1);
				
		}
	}

	private static $addressComponents = array(
		'AddressLine1' => array(
			'premise' => 'long_name',
			'street_address' => 'long_name',
			'route' => 'long_name',
			'intersection' => 'long_name'
		),
		'AddressLine2' => array(
			'administrative_area_level_3' => 'long_name'
		),
		'Town' => array(
			'administrative_area_level_2' => 'long_name',
			'colloquial_area' => 'long_name'
		),
		'City' => array(
			'administrative_area_level_1' => 'long_name',
			'locality' => 'long_name'
		),
		'PostalCode' => array(
			'postal_code' => 'long_name'
		),
		'Country' => array(
			'country' => 'short_name'
		)
	);

	private function loadData( $data ) {
		$this->latitude = $data->geometry->location->lat;
		$this->longitude = $data->geometry->location->lng;

		foreach( $data->address_components As $component ) {
			foreach( static::$addressComponents As $field => $types ) {
				foreach( $types As $type => $property ) {
					if( array_search( $type, $component->types ) !== false ) {
						$this->$field = $component->$property;
					}
				}
			}
		}
	}

	/**
	 */
	public static function getAddressLine1() {
		return $this->addressLine1;
	}

	/**
	 */
	public static function getAddressLine2() {
		return $this->addressLine2;
	}

	/**
	 */
	public static function getTown() {
		return $this->town;
	}

	/**
	 */
	public static function getCity() {
		return $this->city;
	}

	/**
	 */
	public static function getRegion() {
		return $this->region;
	}

	/**
	 */
	public static function getPostalCode() {
		return $this->postalCode;
	}

	/**
	 */
	public static function getCountryCode() {
		return $this->countryCode;
	}

	/**
	 */
	public static function getLat() {
		return $this->latitude;
	}

	/**
	 */
	public static function getLng() {
		return $this->longitude;
	}

}