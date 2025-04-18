<?php
/*
Migrate from fifam ccp-editions-filter to wa-ccp-editions-filter

if editions taxonomy is already registred, defined a constant and define fallback fields names 
*/

if (taxonomy_exists('editions')) {
	define('WA_CCPEF_MIGRATE', true);

	// Define fallback field names
	define('WA_CCPEF_MIGRATE_FIELD_YEAR', 'wpcf-e-year'); // Number
	define('WA_CCPEF_MIGRATE_FIELD_CURRENT', 'wpcf-e-current-edition'); // Checkbox
	define('WA_CCPEF_MIGRATE_FIELD_START', 'wpcf-e-start-date'); // Date
	define('WA_CCPEF_MIGRATE_FIELD_END', 'wpcf-e-end-date'); // Date
	//e-submission-starting-date
	//e-submission-ending-date
	define('WA_CCPEF_MIGRATE_TAXONOMY_FIELD', 'wpcf-select-edition');
} else {
	define('WA_CCPEF_MIGRATE', false);

	// Define field names
	define('WA_CCPEF_MIGRATE_FIELD_YEAR', 'waccpef-e-year'); // Number
	define('WA_CCPEF_MIGRATE_FIELD_CURRENT', 'waccpef-e-current-edition'); // Checkbox
	define('WA_CCPEF_MIGRATE_FIELD_START', 'waccpef-e-start-date'); // Date
	define('WA_CCPEF_MIGRATE_FIELD_END', 'waccpef-e-end-date'); // Date
	//no submissions
	define('WA_CCPEF_MIGRATE_TAXONOMY_FIELD', 'waccpef-select-edition');
}