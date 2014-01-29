<?php

/**
 * Addressable DataObject Decorator
 * Automatically adds a Location tab to the Form as well as a number of fields for use in setting Address details
 */
class AddressableExtension extends DataExtension {

	private static $db = array(
		'Location' => 'GeoLocation'
	);

	/**
	 * Update the CMS FieldList with extra fields
	 * @param FieldList $fields The Fields that will be added to the CMS
	 */
	public function updateCMSFields( FieldList $fields ) {
		$fields->addFieldToTab( 'Root.Location', new GeoLocationField( 'Location' ) );
	}
}