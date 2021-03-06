<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'athletes',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	'athletes\Athlete'              => 'system/modules/athletes/classes/Athlete.php',

	// Models
	'athletes\AthleteCategoryModel' => 'system/modules/athletes/models/AthleteCategoryModel.php',
	'athletes\AthleteModel'         => 'system/modules/athletes/models/AthleteModel.php',

	// Modules
	'athletes\ModuleAthlete'        => 'system/modules/athletes/modules/ModuleAthlete.php',
	'athletes\ModuleAthleteDetail'  => 'system/modules/athletes/modules/ModuleAthleteDetail.php',
	'athletes\ModuleAthleteList'    => 'system/modules/athletes/modules/ModuleAthleteList.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'mod_athlete_detail' => 'system/modules/athletes/templates/modules',
	'mod_athlete_list'   => 'system/modules/athletes/templates/modules',
));
