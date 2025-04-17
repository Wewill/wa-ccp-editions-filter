<?php
/*
Migrate from fifam ccp-editions-filter to wa-ccp-editions-filter

if editions taxonomy is already registred, defined a constant and define fallback fields names 
*/

if (taxonomy_exists('editions')) {
	define('WA_CCPEF_MIGRATE', true);

	// Define fallback field names
	define('WA_CCPEF_MIGRATE_FIELD_NAME_YEAR', 'wpcf-e-year'); // Number
	define('WA_CCPEF_MIGRATE_FIELD_NAME_CURRENT', 'wpcf-e-current-edition'); // Checkbox
	define('WA_CCPEF_MIGRATE_FIELD_NAME_START', 'wpcf-e-start-date'); // Date
	define('WA_CCPEF_MIGRATE_FIELD_NAME_END', 'wpcf-e-end-date'); // Date
	//e-submission-starting-date
	//e-submission-ending-date
} else {
	define('WA_CCPEF_MIGRATE', false);

	// Define field names
	define('WA_CCPEF_MIGRATE_FIELD_NAME_YEAR', 'waccpef-e-year'); // Number
	define('WA_CCPEF_MIGRATE_FIELD_NAME_CURRENT', 'waccpef-e-current-edition'); // Checkbox
	define('WA_CCPEF_MIGRATE_FIELD_NAME_START', 'waccpef-e-start-date'); // Date
	define('WA_CCPEF_MIGRATE_FIELD_NAME_END', 'waccpef-e-end-date'); // Date
	//no submissions
}