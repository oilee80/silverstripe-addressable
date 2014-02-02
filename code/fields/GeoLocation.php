<?php

/**
 * Geo Location class
 * Will store the following fields in the database:
 *  - AddLine1
 *  - AddLine2
 *  - Town
 *  - City
 *  - Region
 *  - PostalCode
 *  - CountryCode 
 */
class GeoLocation extends DBField implements CompositeDBField {

	const	API_RESPONSE_OK = 'OK',
			API_RESPONSE_ZERO_RESULTS = 'ZERO_RESULTS',
			API_RESPONSE_OVER_QUERY_LIMIT = 'OVER_QUERY_LIMIT',
			API_RESPONSE_REQUEST_DENIED = 'REQUEST_DENIED',
			API_RESPONSE_INVALID_REQUEST = 'INVALID_REQUEST',
			API_RESPONSE_UNKNOWN_ERROR = 'UNKNOWN_ERROR';

	private static $GoogleAPIKey;

	protected	$addLine1,
				$addLine2,
				$town,
				$city,
				$region,
				$postalCode,
				$countryCode,
				$automaticLocation,
				$lat,
				$lng;

	protected $isChanged = false;

	static $composite_db = array(
		'_AddLine1' => 'Varchar',
		'_AddLine2' => 'Varchar',
		'_Town' => 'Varchar',
		'_City' => 'Varchar',
		'_Region' => 'Varchar',
		'_PostalCode' => 'Varchar',
		'_CountryCode' => 'Varchar(2)',
		'_AutomaticLocation' => 'Boolean',
		'_Lat' => 'Int',
		'_Lng' => 'Int',
	);

	function requireField() {
		DB::requireField( $this->tableName, "{$this->name}_AddLine1", 'Text' );
		DB::requireField( $this->tableName, "{$this->name}_AddLine2", 'Text' );
		DB::requireField( $this->tableName, "{$this->name}_Town", 'Text' );
		DB::requireField( $this->tableName, "{$this->name}_PostalCode", 'Text' );
		DB::requireField( $this->tableName, "{$this->name}_CountryCode", 'Text' );
		DB::requireField( $this->tableName, "{$this->name}_Lat", 'Text' );
		DB::requireField( $this->tableName, "{$this->name}_Lng", 'Text' );
	}

	function writeToManipulation( &$manipulation ) {
		$name = $this->name;

		$manipulation[ 'fields' ][ $name . '_AddLine1' ] = $this->prepValueForDB( $this->getAddLine1() );
		$manipulation[ 'fields' ][ $name . '_AddLine2' ] = $this->prepValueForDB( $this->getAddLine2() );
		$manipulation[ 'fields' ][ $name . '_Town' ] = $this->prepValueForDB( $this->getTown() );

		if($this->getCity()) {
			$manipulation['fields'][ $name . '_City'] = $this->prepValueForDB($this->getCity() );
		} else {
			$manipulation['fields'][ $name . '_City'] = DBField::create_field( 'Varchar', $this->getCity() )->nullValue();
		}

		if( $this->getRegion() ) {
			$manipulation['fields'][ $name . '_Region'] = $this->prepValueForDB($this->getRegion() );
		} else {
			$manipulation['fields'][ $name . '_Region'] = DBField::create_field( 'Varchar', $this->getRegion() )->nullValue();
		}

		$manipulation[ 'fields' ][ $name . '_CountryCode' ] = $this->prepValueForDB( $this->getCountryCode() );
		$manipulation[ 'fields' ][ $name . '_PostalCode' ] = $this->prepValueForDB( $this->getPostalCode() );
		$manipulation[ 'fields' ][ $name . '_AutomaticLocation' ] = $this->prepValueForDB( $this->getAutomaticLocation() );
		$manipulation[ 'fields' ][ $name . '_Lat' ] = $this->prepValueForDB( $this->getLat() );
		$manipulation[ 'fields' ][ $name . '_Lng' ] = $this->prepValueForDB( $this->getLng() );

	}

	public function compositeDatabaseFields() {
		return static::$composite_db;
	}

	function addToQuery(&$query) {
		parent::addToQuery($query);
		$query->setSelect( "{$this->name}_AddLine1" );
		$query->setSelect( "{$this->name}_AddLine2" );
		$query->setSelect( "{$this->name}_Town" );
		$query->setSelect( "{$this->name}_City" );
		$query->setSelect( "{$this->name}_Region" );
		$query->setSelect( "{$this->name}_PostalCode" );
		$query->setSelect( "{$this->name}_AutomaticLocation" );
		$query->setSelect( "{$this->name}_Lat" );
		$query->setSelect( "{$this->name}_Lng" );
	}

	public function getValue() {
Debug::dump( __METHOD__ );die();
	}

	function setValue( $value, $record = null, $markChanged=true ) {

		if ( $value instanceof GeoLocation && $value->exists() ) {

			$this->setAddLine1( $value->getAddLine1(), $markChanged );
			$this->setAddLine2( $value->getAddLine2(), $markChanged );
			$this->setTown( $value->getTown(), $markChanged );
			$this->setCity( $value->getCity(), $markChanged );
			$this->setRegion( $value->getRegion(), $markChanged );
			$this->setCountryCode( $value->getCountryCode(), $markChanged );
			$this->setPostalCode( $value->getPostalCode(), $markChanged );
			$this->setAutomaticLocation( $value->getAutomaticLocation(), $markChanged );
			$latLng = $this->getLatLng();
			$this->setLat( $latLng->Lat, $markChanged );
			$this->setLng( $latLng->Lng, $markChanged );

			if( $markChanged ) $this->isChanged = true;
	
		} else if(
			$record &&
			is_array( $record ) &&
			isset( $record[ $this->name . '_AddLine1' ] ) &&
			isset( $record[ $this->name . '_AddLine2' ] ) &&
			isset( $record[ $this->name . '_Town' ] ) &&
			isset( $record[ $this->name . '_PostalCode' ] ) &&
			isset( $record[ $this->name . '_CountryCode' ] ) &&
			isset( $record[ $this->name . '_Lat' ] ) &&
			isset( $record[ $this->name . '_Lng' ] )
		) {
			$this->setAddLine1( $record[ $this->name . '_AddLine1' ], $markChanged );
			$this->setAddLine2( $record[ $this->name . '_AddLine2' ], $markChanged );
			$this->setTown( $record[ $this->name . '_Town' ], $markChanged );
			$this->setCity( ( isset( $record[ $this->name . '_City' ] ) ) ? $record[ $this->name . '_City' ] : null, $markChanged );
			$this->setRegion( ( isset( $record[ $this->name . '_Region' ] ) ) ? $record[ $this->name . '_Region' ] : null, $markChanged );
			$this->setPostalCode( $record[ $this->name . '_PostalCode' ], $markChanged );
			$this->setCountryCode( $record[ $this->name . '_CountryCode' ], $markChanged );
			$this->setAutomaticLocation( $record[ $this->name . '_AutomaticLocation' ], $markChanged );
			if( !$record[ $this->name . '_AutomaticLocation' ] ) {
				$this->setLat( $record[ $this->name . '_Lat' ], $markChanged );
				$this->setLng( $record[ $this->name . '_Lng' ], $markChanged );
			} else {
			$latLng = $this->getLatLng();
			$this->setLat( $latLng->Lat, $markChanged );
			$this->setLng( $latLng->Lng, $markChanged );
			}

			if( $markChanged ) $this->isChanged = true;
	
		} else if ( is_array( $value ) ) {
	
			if( $markChanged ) $this->isChanged = true;
	
		}
	}

	private function setAutomaticLatLng( $markChanged = true) {
		$latlng = $this->getLatLng();
		if( is_a( $latlng, 'StdClass') ) {
			$this->setLat( $latlng->Lat, $markChanged);
			$this->setLng( $latlng->Lng, $markChanged);
		}
	}

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
	 * Use Google Maps API to determine the Latitude & Longtitude from the 
	 * If the field hasn't changed then just return the existing Lat & Lng values (don't bother requesting from Google Maps API)
	 * @return StdClass
	 */
	private function getLatLng() {
		if( $this->getAutomaticLocation() && ( $this->isChanged() || ( !$this->getLat() && !$this->getLng() ) ) ) {
			$addressStr = $this->getAddLine1() . ' ' . $this->getStreetName() . ',' . $this->getTown();
			$addressStr.= ( $this->getCity() ) ? ',' . $this->getCity() : '';
			$addressStr.= ( $this->getRegion() ) ? ',' . $this->getRegion() : '';
			$addressStr.= ',' . $this->getPostalCode() . ',' . $this->getCountryCode();
			$req = sprintf( 'http://maps.googleapis.com/maps/api/geocode/json?address=%s&sensor=false', urlencode( $addressStr ) );
			if( !( $response = @file_get_contents( $req ) ) ) {
				throw new Exception( sprintf( 'Unable to contact (%s)', $req ) );
			}
			$geoCode = json_decode( $response );
// Do a switch here based on the possible status messages returned
			if( is_object( $geoCode ) ) {
				switch( $geoCode->status ) {
					case static::API_RESPONSE_ZERO_RESULTS:
						// ZERO RESULTS
						break;
					case static::API_RESPONSE_OK:
						$o = new StdClass();
						$o->Lat = $geoCode->results[0]->geometry->location->lat;
						$o->Lng = $geoCode->results[0]->geometry->location->lng;
						return $o;
				}
			} else {
				throw new Exception( sprintf( 'Invalid response (%s) from (%s)', $response, $req ) );
			}
		} else {
			$o = new StdClass();
			$o->Lat = $this->getLat();
			$o->Lng = $this->getLng();
			return $o;
		}
	}

/***********
**  Setters  **
************/

	/**
	 * Set the AddLine1 Name/Number
	 * @param String $val AddLine1 Name/Number
	 */
	public function setAddLine1($val, $markChanged=true) {
		$this->addLine1 = $val;
		if( $markChanged ) $this->isChanged = true;
	}

	/**
	 * Set the AddLine2
	 * @param String $val AddLine2
	 */
	public function setAddLine2( $val, $markChanged=true ) {
		$this->addLine2 = $val;
		if( $markChanged ) $this->isChanged = true;
	}

	/**
	 * Set the Town
	 * @param String $val Town
	 */
	public function setTown( $val, $markChanged=true ) {
		$this->town = $val;
		if( $markChanged ) $this->isChanged = true;
	}

	/**
	 * Set the City
	 * @param String $val City
	 */
	public function setCity( $val, $markChanged=true ) {
		$this->city = $val;
		if( $markChanged ) $this->isChanged = true;
	}

	/**
	 * Set the Region
	 * @param String $val Region
	 */
	public function setRegion( $val, $markChanged=true ) {
		$this->region = $val;
		if( $markChanged ) $this->isChanged = true;
	}

	/**
	 * Set the Postal Code
	 * @param String $val Postal Code
	 */
	public function setPostalCode( $val, $markChanged=true ) {
		$this->postalCode = $val;
		if( $markChanged ) $this->isChanged = true;
	}

	/**
	 * Set the Country Code
	 * @param String $val Country Code
	 */
	public function setCountryCode( $val, $markChanged=true ) {
		$this->countryCode = $val;
		if( $markChanged ) $this->isChanged = true;
	}

	/**
	 * Set the Automatic Location
	 * @param Boolean $val Automatic Location
	 */
	public function setAutomaticLocation( $val, $markChanged=true ) {
		$this->automaticLocation = $val;
		if( $markChanged ) $this->isChanged = true;
	}

	/**
	 * Set the Latitude
	 * @param Int $val Latitude
	 */
	public function setLat( $val, $markChanged=true ) {
		$this->lat = $val;
		if( $markChanged ) $this->isChanged = true;
	}

	/**
	 * Set the Lng
	 * @param Int $val Lng
	 */
	public function setLng( $val, $markChanged=true ) {
		$this->lng = $val;
		if( $markChanged ) $this->isChanged = true;
	}

/***********
**  Getters  **
************/
	/**
	 */
	public function getAddLine1() {
		return (string)$this->addLine1;
	}

	public function getAddLine2() {
		return (string)$this->addLine2;
	}

	public function getTown() {
		return (string)$this->town;
	}

	public function getCity() {
		return (string)$this->city;
	}

	public function getRegion() {
		return (string)$this->region;
	}

	public function getCountryCode() {
		return (string)$this->countryCode;
	}

	public function getPostalCode() {
		return (string)$this->postalCode;
	}

	public function getAutomaticLocation() {
		return (bool)$this->automaticLocation;
	}

	public function getLat() {
		return (int)$this->lat;
	}

	public function getLng() {
		return (int)$this->lng;
	}

	public static function setGoogleAPIKey( $key ) {
		static::$GoogleAPIKey = $key;
	}

	function isChanged() {
		return $this->isChanged;
	}

	/**
	 * Check if the Field has been set
	 * Only bother checking the required fields
	 * $return Boolean
	 */
	function exists() {
		$ret = (bool) (
			$this->getAddLine1() ||
			$this->getAddLine2() ||
			$this->getTown() ||
			$this->getPostalCode() ||
			$this->getLat() ||
			$this->getLng()
		);
		return $ret;
	}
}
