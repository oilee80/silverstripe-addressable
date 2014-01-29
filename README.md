silverstripe-addressable
========================

Add Address &amp; Geocode fields to Database &amp; Form

This module can add extra fields to a DataObject giving it the ability to have Geocoded data stored against it

Usage
=====

```php
Object::add_extension( 'ClassName', 'AddressableExtension' );
```

OR

```php
class ClassName extends DataObject {
...
	private static $extensions = array(
		'AddressableExtension'
	);
...
}
```

This will add the following fields to the Database

* Location_AddLine1
* Location_AddLine2
* Location_Town
* Location_City
* Location_Region
* Location_PostalCode
* Location_CountryCode
* Location_AutomaticLocationField
* Location_Lat
* Location_Lng

As well a new Tab to the Admin Screen with corresponding Fields

* Address Line 1
* Address Line 2
* Town
* City
* Region
* Postal Code
* Country
* Automatically Determine Location
* Latitude
* Longitude

A Google map will also be shown with a pointer on the set point