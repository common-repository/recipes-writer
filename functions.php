<?php

 /*  Copyright 2014  Marc-André Larivière  (email : marc@smellmykitchen.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

global $wpdb;
define('REWR_RECIPES_TABLE', $wpdb->prefix . "rewrrecipes");
define('REWR_OPTIONS_TABLE', $wpdb->prefix . "rewroptions");
define('REWR_CATEGORIES_TABLE', $wpdb->prefix . "rewrcategories");
define('REWR_CUISINES_TABLE', $wpdb->prefix . "rewrcuisines");

/*****************************/
/*
/* RECIPES FUNCTIONS
/*
/*****************************/


//Get the list of recipes from the database
function GetRecipesList( $sort = 'id', $search = null ) {
	global $wpdb;
	
	if ( $search === null )	{
		$results = $wpdb->get_results( 'SELECT * FROM ' . constant('REWR_RECIPES_TABLE') . ' ORDER BY '. $sort .'; ', OBJECT );
	} else {
		$results = $wpdb->get_results( 'SELECT * FROM ' . constant('REWR_RECIPES_TABLE') . ' WHERE name LIKE "%' . $search . '%" ORDER BY '. $sort .'; ', OBJECT );
	}
	
	return $results;
}

//Get one recipe
function GetRecipe( $id = null ) {
	global $wpdb;
	
	if ( $id !== null ) {
		$result = $wpdb->get_row( 'SELECT * FROM ' . constant('REWR_RECIPES_TABLE') . ' WHERE id="' . $id . '"', OBJECT );
	}

	return $result;
}

//Save a recipe
function SaveRecipe( $data ) {
	global $wpdb;
	
	$defaults = array(
		'id' => null,
		'name' => null, //Mandatory
		'image' => null,
		'imageid' => null,
		'categoryid' => null,
		'cuisineid' => null,
		'description' => null,
		'author' => null,
		'preparationtime' => null,
		'cooktime' => null,
		'totaltime' => null,
		'servings' => null,
		'ingredients' => null,
		'instructions' => null,
		'note' => null
	);

	// Parse incoming $data into an array and merge it with $defaults 
	$data = wp_parse_args( $data, $defaults );

	// If name for the recipe is not set, we return an error.
	if ( $data['name'] == null ) {
		return false;	
	} else { //Else we save the recipe	
		if ( $data['id'] === null ) {
			//No ID? This is a new recipe
			$wpdb->insert(constant('REWR_RECIPES_TABLE'), $data);
			$id = $wpdb->insert_id;
		} else {
			//Update since it's an old recipe
			$wpdb->update(constant('REWR_RECIPES_TABLE'), $data, array ( 'id'=>$data['id'] ) );
			$id = $data['id'];
		}
	
		return $id;
	}
}

//Update rating
function UpdateRating( $data ) {
	global $wpdb;
	
	$defaults = array(
		'id' => null,
		'ratingtotal' => null,
		'ratingcount' => null
	);

	// Parse incoming $data into an array and merge it with $defaults 
	$data = wp_parse_args( $data, $defaults );

	// If we don't have the id of the recipe we exit
	if ( $data['id'] == null ) {
		return false;	
	} else { //Else we update the rating
		return $wpdb->update(constant('REWR_RECIPES_TABLE'), $data, array ( 'id'=>$data['id'] ) );
	}
}

//Get the total rating (all the ratings added together)
function GetRatingTotal( $id = null ) {
	global $wpdb;
	
	if ( $id !== null ) {
		$result = $wpdb->get_row( 'SELECT * FROM ' . constant('REWR_RECIPES_TABLE') . ' WHERE id="' . $id . '"', OBJECT );
	}

	return $result->ratingtotal;
}

//Get the number of rating
function GetRatingCount( $id = null ) {
	global $wpdb;
	
	if ( $id !== null ) {
		$result = $wpdb->get_row( 'SELECT * FROM ' . constant('REWR_RECIPES_TABLE') . ' WHERE id="' . $id . '"', OBJECT );
	}

	return $result->ratingcount;
}

//Delete the recipe by id
function DeleteRecipe( $id ) {
	global $wpdb;
	
	$wpdb->query( 
		$wpdb->prepare( "DELETE FROM " . constant('REWR_RECIPES_TABLE') . " WHERE id = " . $id )
	);
}



/*********************/
/*
/* OPTIONS FUNCTIONS
/* 
/*********************/

//Get option from the database
function GetOption( $optionName ) {
	global $wpdb;
	
	$result = $wpdb->get_row( 'SELECT * FROM ' . constant('REWR_OPTIONS_TABLE') . ' WHERE optionname="' . $optionName . '"', OBJECT );
	
	return $result->data;
}

//Save option in the database
function SaveOption( $data ) {
	global $wpdb;
	
	$defaults = array(
			'optionname' => null,
			'data' => null
		);
	
	// Parse incoming $data into an array and merge it with $defaults 
	$data = wp_parse_args( $data, $defaults );
	
	if ( $data['optionname'] !== null ) {
		$results = $wpdb->update(constant('REWR_OPTIONS_TABLE'), $data, array ( 'optionname'=>$data['optionname'] ) );
	} else {
		return false;	
	}
	
	return $results;
}

//Get the list of categories from the database and return an array
function GetCategoriesList() {
	global $wpdb;
	
	$results = $wpdb->get_results( 'SELECT * FROM ' . constant('REWR_CATEGORIES_TABLE') . ' ORDER BY id', OBJECT );
	
	//Return only if it's not null
	if ( $wpdb->num_rows > 0 ) {
		return $results;
	} else {
		return null;	
	}
}

//Get the category according to an id
function GetCategory( $id ) {
	global $wpdb;
	
	$result = $wpdb->get_row( 'SELECT * FROM ' . constant('REWR_CATEGORIES_TABLE') . ' WHERE id=' . $id, OBJECT );
	
	return $result->category;
}

//Get the list of cuisines from the database and return an array
function GetCuisinesList() {
	global $wpdb;
	
	$results = $wpdb->get_results( 'SELECT * FROM ' . constant('REWR_CUISINES_TABLE') . ' ORDER BY id', OBJECT );
	
	//Return only if it's not null
	if ( $wpdb->num_rows > 0 ) {
		return $results;
	} else {
		return null;	
	}
}

//Get the cuisine according to an id
function GetCuisine( $id ) {
	global $wpdb;
	
	$result = $wpdb->get_row( 'SELECT * FROM ' . constant('REWR_CUISINES_TABLE') . ' WHERE id=' . $id, OBJECT );
	
	return $result->cuisine;
}

//Save categories
function SaveCategories( $categories ) {
	global $wpdb;
	
	$success = true;
	
	
	foreach ( $categories as $data ) {
	
		$defaults = array(
			'id' => null,
			'category' => null
		);
	
		// Parse incoming $data into an array and merge it with $defaults 
		$data = wp_parse_args( $data, $defaults );

		// Update the database we the new set of categories
		if ( $data['id'] !== null ) {
			if ( $data['category'] == null ) {
				$results = $wpdb->delete(constant('REWR_CATEGORIES_TABLE'), array ( 'id'=>$data['id'] ) );
			} else {
				//We update an old category
				$results = $wpdb->update(constant('REWR_CATEGORIES_TABLE'), $data, array ( 'id'=>$data['id'] ) );
			}
			
			if ( $results === false ) {
				$success = false;	
			}
		} else {
			//If ID is null, this is a new category
			$results = $wpdb->insert(constant('REWR_CATEGORIES_TABLE'), $data);

			if ( $results === false ) {
				$success = false;	
			}
		}
	}
	
	return $success;
}

//Save cuisines
function SaveCuisines( $cuisines ) {
	global $wpdb;
	
	$success = true;
	
	
	foreach ( $cuisines as $data ) {
	
		$defaults = array(
			'id' => null,
			'cuisine' => null
		);
	
		// Parse incoming $data into an array and merge it with $defaults 
		$data = wp_parse_args( $data, $defaults );

		// Save the cuisine to the database
		if ( $data['id'] !== null ) {
			if ( $data['cuisine'] == null ) {
				$results = $wpdb->delete(constant('REWR_CUISINES_TABLE'), array ( 'id'=>$data['id'] ) );
			} else {
				//We update an old category
				$results = $wpdb->update(constant('REWR_CUISINES_TABLE'), $data, array ( 'id'=>$data['id'] ) );
			}
			
			if ( $results === false ) {
				$success = false;	
			}
		} else {
			//If ID is null, this is a new cuisine
			$results = $wpdb->insert(constant('REWR_CUISINES_TABLE'), $data);

			if ( $results === false ) {
				$success = false;	
			}
		}
	}
	
	return $success;
}



//Get all the templates names
function GetTemplates() {
	$uploadDir = wp_upload_dir();
 	$templateDir = $uploadDir['basedir'] . '/recipes-writer-templates';
 	
	$templates = array();
	$files = scandir( $templateDir );
	
	foreach ( $files as $file ) 
	{
		$split = explode( '.', $file );

		if ( $split[1] == 'php' ) {
			array_push( $templates, $split[0] );
		}
	}
	
	return $templates;
}

/********************/
/*
/* OTHER
/*
/********************/

//Unit translation from full to abbreviation
function AbbreviateUnit( $unit ) {
	switch( $unit ) {
		case 'ounce':
		case 'ounces':
			return 'oz';
		case 'pound':
		case 'pounds':
			return 'lb';
		case 'gram':
		case 'grams':
			return 'g';
		case 'kilogram':
		case 'kilograms':
			return 'kg';
		case 'millilitre':
		case 'millilitres':
			return 'ml';
		case 'litre':
		case 'litres':
			return 'l';
		case 'tablespoon':
		case 'tablespoons':
			return 'tbsp';
		case 'teaspoon':
		case 'teaspoons':
			return 'tsp';
		case 'cup':
		case 'cups':
			return 'c';
		default:
			return $unit;
	}	
} ?>