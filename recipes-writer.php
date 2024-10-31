<?php
/**
 * Plugin Name: Recipes Writer
 * Plugin URI: http://smellmykitchen.com/recipes-writer/
 * Description: Write recipes for your blog. This plugin will help you write standardized recipes. It is easy, intuitive, secure and Google rich snippets compliant.
 * Version: 1.0.4
 * Author: Marc-André Larivière
 * Author URI: http://smellmykitchen.com
 * License: GPL2
 */
 
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
 
global $rewr_db_version;
$rewr_db_version = "1.0"; //Current version of the plugin's database.
 
/*
	Installs the plugin. Creates the database needed for the plugin to run.
*/
function rewr_install() {
	global $wpdb;
	global $rewr_db_version;
 	
 	$installed_version = get_option( "rewr_db_version" );
 	
 	if ( $installed_version != $rewr_db_version || $installed_version == null ) {
 	
 		//Recipes database
	 	$table_name = $wpdb->prefix . "rewrrecipes";
	 	
	 	$sql = "CREATE TABLE $table_name (
	  		id mediumint(9) NOT NULL AUTO_INCREMENT,
	  		name varchar(255) NOT NULL,
	  		image varchar(255),
	  		imageid mediumint(9),
	  		categoryid mediumint(9),
	  		cuisineid mediumint(9),
	  		description text,
	  		author varchar(255),
	  		preparationtime mediumint(9),
	  		cooktime mediumint(9),
	  		totaltime mediumint(9),
	  		servings mediumint(9),
	  		ingredients text,
	  		instructions text,
	  		note text,
	  		ratingtotal mediumint(9),
	  		ratingcount mediumint(9),
	  		UNIQUE KEY id (id)
			);";
	
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
		
		//Options database
		$table_name = $wpdb->prefix . "rewroptions";
	 	
	 	$sql = "CREATE TABLE $table_name (
	 		id mediumint(9) NOT NULL AUTO_INCREMENT,
	  		optionname varchar(255) NOT NULL,
	  		data text,
	  		UNIQUE KEY id (id)
			);";
	
		dbDelta( $sql );
		
		if ( $installed_version == null ) {
			$wpdb->insert( $table_name, array( 'id'=>null, 'optionname'=>'mass', 'data'=>'imperial' ) );
			$wpdb->insert( $table_name, array( 'id'=>null, 'optionname'=>'liquid', 'data'=>'customary' ) );
			$wpdb->insert( $table_name, array( 'id'=>null, 'optionname'=>'temperature', 'data'=>'fahrenheit' ) );
			$wpdb->insert( $table_name, array( 'id'=>null, 'optionname'=>'smallform', 'data'=>'false' ) );
			$wpdb->insert( $table_name, array( 'id'=>null, 'optionname'=>'template', 'data'=>'default' ) );
			$wpdb->insert( $table_name, array( 'id'=>null, 'optionname'=>'hideimage', 'data'=>'false' ) );
			$wpdb->insert( $table_name, array( 'id'=>null, 'optionname'=>'userating', 'data'=>'true' ) );
			$wpdb->insert( $table_name, array( 'id'=>null, 'optionname'=>'showreviewcount', 'data'=>'true' ) );
			$wpdb->insert( $table_name, array( 'id'=>null, 'optionname'=>'displayrewrfooter', 'data'=>'false' ) );
			$wpdb->insert( $table_name, array( 'id'=>null, 'optionname'=>'defaultauthor', 'data'=>'' ) );
			$wpdb->insert( $table_name, array( 'id'=>null, 'optionname'=>'printbuttonlabel', 'data'=>'Print' ) );
		}
		
		
		//Categories database
		$table_name = $wpdb->prefix . "rewrcategories";
	 	
	 	$sql = "CREATE TABLE $table_name (
	  		id mediumint(9) NOT NULL AUTO_INCREMENT,
	  		category varchar(255) NOT NULL,
	  		UNIQUE KEY id (id)
			);";
	
		dbDelta( $sql );
		
		if ( $installed_version == null ) {
			$wpdb->insert( $table_name, array( 'id'=>null, 'category'=>'Lunch' ) );
			$wpdb->insert( $table_name, array( 'id'=>null, 'category'=>'Dinner' ) );
		}
		
		//Cuisines database
		$table_name = $wpdb->prefix . "rewrcuisines";
	 	
	 	$sql = "CREATE TABLE $table_name (
	  		id mediumint(9) NOT NULL AUTO_INCREMENT,
	  		cuisine varchar(255) NOT NULL,
	  		UNIQUE KEY id (id)
			);";
	
		dbDelta( $sql );
		
		if ( $installed_version == null ) {
			$wpdb->insert( $table_name, array( 'id'=>null, 'cuisine'=>'American' ) );
			$wpdb->insert( $table_name, array( 'id'=>null, 'cuisine'=>'Canadian' ) );
			$wpdb->insert( $table_name, array( 'id'=>null, 'cuisine'=>'Chinese' ) );
		}

		if ( $installed_version == null ) {
			add_option( "rewr_db_version", $rewr_db_version );
		} else {
			update_option( "rewr_db_version", $rewr_db_version );
		}
	
 	}
}

/*
	Checks if the plugin database version is higher than the installed version.
*/
function rewr_update_db_check() {
	global $rewr_db_version;
	
	//If the current database version is different that the plugin version we update the database
	if ( get_option( 'rewr_db_version' ) != $rewr_db_version ) {
		rewr_install();	
	}
	
	//Create the templates folder and move the templates over
 	$uploadDir = wp_upload_dir();
 	$templateDir = $uploadDir['basedir'] . '/recipes-writer-templates';

 	if ( !is_dir( $templateDir ) ) {
 		wp_mkdir_p( $templateDir );
 	}
 	
 	//Copy templates over to the uploads directory
	$files = scandir( plugin_dir_path( __FILE__ ) . 'templates/' );
	
	foreach ( $files as $file ) 
	{
		@copy( plugin_dir_path( __FILE__ ) . 'templates/' . $file, $templateDir . '/' . $file );
	}
}
add_action( 'plugins_loaded', 'rewr_update_db_check' );

register_activation_hook( __FILE__, 'rewr_install' );

/*
	Creates the option menu
*/
function rewr_menu() {
	// Create the Recipes Writer admin menu
	global $recipes_writer_page; // Added global vars
	global $recipes_options_page;
	
	add_menu_page( 'Recipes Writer - Recipes', 'Recipes Writer', 'donotshow', 'rewr_admin', null, null );
	
	$recipes_writer_page = add_submenu_page( 'rewr_admin', 'Recipes Writer - Recipes', 'Recipes', 'publish_posts', 'rewr_recipes', 'rewr_recipes');
	$recipes_options_page = add_submenu_page( 'rewr_admin', 'Recipes Writer - Options', 'Options', 'publish_posts', 'rewr_options', 'rewr_options');
	
	//This is to make sure the templates are transfered on plugin upgrade...
	$uploadDir = wp_upload_dir();
 	$templateDir = $uploadDir['basedir'] . '/recipes-writer-templates';
	if ( !is_dir( $templateDir ) ) {
 		wp_mkdir_p( $templateDir );
 		
 		//Copy templates over to the uploads directory
		$files = scandir( plugin_dir_path( __FILE__ ) . 'templates/' );
		
		foreach ( $files as $file ) 
		{
			@copy( plugin_dir_path( __FILE__ ) . 'templates/' . $file, $templateDir . '/' . $file );
		}
 	}
}
add_action( 'admin_menu', 'rewr_menu' );

/*
	Loads the options menu
*/
function rewr_options() {
	if ( !current_user_can( 'publish_posts' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	require_once( __DIR__ . '/functions.php' );
	require_once( __DIR__ . '/admin-options.php' );
}

/*
	Loads the recipes menu
*/
function rewr_recipes() {
	if ( !current_user_can( 'publish_posts' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	require_once( __DIR__ . '/functions.php' );
	require_once( __DIR__ . '/admin-recipes.php' );	
}

//Load CSS and JS files
function loadFiles($hook)
{
	global $recipes_writer_page;
	global $recipes_options_page;
	 
	if( $hook != $recipes_writer_page && $hook != $recipes_options_page)  // only continue to load if recipes admin pages.
	return;
	
	wp_enqueue_script( 'recipes-writer-js', plugins_url( 'recipes-writer.js', __FILE__ ) );
	
	if ( !wp_script_is('jquery') ) {
		wp_enqueue_script( 'jquery' );	
	}
	
	if ( !wp_script_is('jquery-ui-sortable') ) {
		wp_enqueue_script( 'jquery-ui-sortable' );	
	}
	
	wp_enqueue_script( 'jquery-caret-js', plugins_url( '/inc/jquery.caret.js', __FILE__ ) );
	wp_enqueue_style( 'recipes-writer-css', plugins_url( 'recipes-writer.css', __FILE__ ) );
	wp_enqueue_media();
	wp_enqueue_script( 'custom-header' );
}
add_action( 'admin_enqueue_scripts', 'loadFiles' );

function loadFilesFrontEnd()
{
	wp_enqueue_script( 'recipes-writer-js', plugins_url( 'recipes-writer.js', __FILE__ ) );
	
	if ( !wp_script_is('jquery') ) {
		wp_enqueue_script( 'jquery' );	
	}
	
	wp_enqueue_style( 'recipes-writer-css', plugins_url( 'recipes-writer.css', __FILE__ ) );
}
add_action( 'wp_enqueue_scripts', 'loadFilesFrontEnd', 99 );

//Image size
add_image_size( 'rewr-image-thumb', 250, 250, true );

//Shortcode to display the recipe in a blog post
function DisplayRecipe( $atts ){
	require_once( __DIR__ . '/functions.php' );	

	$data = shortcode_atts( array(
        'id' => null,
        'template' => GetOption( 'template' )
    ), $atts );

    if ( $data['id'] !== null ) {
    	global $rewrID;
    	$rewrID .= $data['id'] . ',';
    	
    	$url = strtok($_SERVER["REQUEST_URI"], '?');
		$urlArray = explode( '/', rtrim( $url, '/' ) );
		$lastIndex = count( $urlArray ) - 1;
		
	   	if( $urlArray[ $lastIndex -1 ] == 'rewr-print' ) {
	   		$isPrint = true;
	   	} else {
	   		$isPrint = false;	
	   	}
    	
    	//Load the recipe
    	$recipeData = GetRecipe( $data['id'] );
    	
    	//Load the template and process it
    	$uploadDir = wp_upload_dir();
 		$templateDir = $uploadDir['baseurl'] . '/recipes-writer-templates';
    	$template = $templateDir . '/' . $data['template'] . '.php';
    	
    	$html = file_get_contents( $template );
    	
    	//Replace the template variables with the proper information
    	
    	//Recipe class and itemscope
    	$html = str_replace( '{$recipe}', 'class="rewr-recipe" itemscope itemtype="http://schema.org/Recipe"', $html );
    	
    	//Print button
    	if ( !$isPrint ) {
    		$html = str_replace( '{$print}', '<a rel="nofollow" class="print-button" href="' . home_url( 'rewr-print/' . $recipeData->id ) . '?url=' . get_permalink() . '"><img src="' . plugins_url( 'icons/print.png', __FILE__ ) . '">' . GetOption( 'printbuttonlabel' ) . '</a>', $html );
    	} else {
    		$html = str_replace( '{$print}', '', $html );
    	}
    	
    	//Share buttons
    	if ( !$isPrint ) {
    		$url = get_permalink();
    		$shareHtml = '';
    		if ( $recipeData->image !== '' ) {
    			$shareHtml .= '<a target="_blank" href="https://pinterest.com/pin/create/unknown/?url=' . $url . '&media=' . $recipeData->image . '&description=' . $recipeData->name . '"><img class="share-icon" src="' . plugins_url( 'share/pinterest.png', __FILE__ ) . '" alt="Share on Pinterest"></a>'; //Pinterest
    		}
    		$shareHtml .= '<a target="_blank" href="https://twitter.com/home?status=' . $recipeData->name . ' - ' . $url . '"><img class="share-icon" src="' . plugins_url( 'share/twitter.png', __FILE__ ) . '" alt="Share on Twitter"></a>'; //Twitter
    		$shareHtml .= '<a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=' . $url . '"><img class="share-icon" src="' . plugins_url( 'share/facebook.png', __FILE__ ) . '" alt="Share on Facebook"></a>'; //Facebook
    		$shareHtml .= '<a target="_blank" href="https://plus.google.com/share?url=' . $url . '"><img class="share-icon" src="' . plugins_url( 'share/googleplus.png', __FILE__ ) . '" alt="Share on Google+"></a>'; //Google+
    		$html = str_replace( '{$share}', $shareHtml, $html );
    	} else {
    		$html = str_replace( '{$share}', '', $html );
    	}
    	
    	//Url button
    	if ( $isPrint ) {
    		$url = $_GET['url'];
    		$html = str_replace( '{$url}', $url, $html );
    	} else {
    		$html = str_replace( '{$url}', '', $html );
    	}
    	
    	//Blog's name
    	$html = str_replace( '{$blogname}', get_bloginfo( 'name' ), $html );
    	
    	//Recipe's name
    	$html = str_replace( '{$name}', '<span itemprop="name">' . $recipeData->name . '</span>', $html );
    	
    	//Image
    	if ( $recipeData->image !== '' ) {
    		$html = str_replace( '{$image}', '<img class="recipe-image" itemprop="image" src="' . $recipeData->image . '" alt="' . $recipeData->name . '">', $html );
    	} else {
    		$html = str_replace( '{$image}', '', $html );
    	}
    	//Description
    	$html = str_replace( '{$description}', '<span itemprop="description">' . $recipeData->description . '</span>', $html );
    	
    	//Author
    	$html = str_replace( '{$author}', '<span itemprop="author">' . $recipeData->author . '</span>', $html );
    	
    	//Servings
    	$html = str_replace( '{$servings}', '<span itemprop="recipeYield">' . $recipeData->servings . '</span>', $html );
    	
    	//Preparation Time
    	$html = str_replace( '{$preptime}', '<span itemprop="prepTime" content="PT' . $recipeData->preparationtime . 'M">' . $recipeData->preparationtime . '</span>', $html );
    	
    	//Cooking Time
    	$html = str_replace( '{$cooktime}', '<span itemprop="cookTime" content="PT' . $recipeData->cooktime . 'M">' . $recipeData->cooktime . '</span>', $html );
    	
    	//Total Time
    	$html = str_replace( '{$totaltime}', '<span itemprop="totalTime" content="PT' . $recipeData->totaltime . 'M">' . $recipeData->totaltime . '</span>', $html );
    	
    	//Category
    	$html = str_replace( '{$category}', '<span itemprop="recipeCategory">' . GetCategory( $recipeData->categoryid ) . '</span>', $html );
    	
    	//Cuisine
    	$html = str_replace( '{$cuisine}', '<span itemprop="recipeCuisine">' . GetCuisine( $recipeData->cuisineid ) . '</span>', $html );
    	
    	//Rating
    	if ( GetOption( 'userating' ) == 'true' && GetRatingCount( $recipeData->id ) != 0 ) {
    		$ratingtotal = GetRatingTotal( $recipeData->id );
    		$ratingcount = GetRatingCount( $recipeData->id );
    		$rating = round( $ratingtotal / $ratingcount , 0, PHP_ROUND_HALF_UP );
    		$stars = '';
    		for ( $i = 1; $i <= 5; $i++ ) {
    			if ( $i <= $rating ) {
    				$stars .= '<img class="rewr-rating-icon" src="' . plugins_url( 'icons/star-on.png', __FILE__ ) . '" alt="&star;" nopin="nopin">';
    			} else {
    				$stars .= '<img class="rewr-rating-icon" src="' . plugins_url( 'icons/star-off.png', __FILE__ ) . '" alt="&star;" nopin="nopin">';
    			}
    		}

    		$ratinghtml = '<span itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">';
    		$ratinghtml .= '<span class="rewr-recipe-rating-hide" itemprop="ratingValue">' . $rating . '</span>';
    		$ratinghtml .= $stars;
    		$ratinghtml .= '<span';
    		if ( GetOption( 'showreviewcount' ) == 'false' ) {
    			$ratinghtml .= ' class="rewr-recipe-rating-hide"';
    		}
    		$ratinghtml .= '> based on ';
    		$ratinghtml .= '<span itemprop="reviewCount">' . $ratingcount . '</span>';
    		$ratinghtml .= ' reviews</span>';
    		$ratinghtml .= '</span>';
    		$html = str_replace( '{$rating}', $ratinghtml, $html );
    	} else {
    		$html = str_replace( '{$rating}', 'Not rated', $html );
    	}
    	
    	//Ingredients
		if ( $recipeData->ingredients !== "" ) {
			$ingredientsList = '<ul class="ingredients-list">';
			
			$ingredients = explode( '|||', $recipeData->ingredients );
			$useSmallForm = GetOption( 'smallform' );
			
			foreach ( $ingredients as $ingredient ) {
				$info = explode( '||', $ingredient );

				for ( $i = 0; $i < count( $info ); $i++ ) {
					if ( $info[$i] == " " ) {
						$info[$i] = "";	
					}	
				}
				
				if ( $info[0] == 'label' ) {
					$ingredientsList .= '</ul>';
					$ingredientsList .= '<div class="label-ingredients">' . $info[2] . '</div>';
					$ingredientsList .= '<ul>';
				} else {
					$ingredientsList .= '<li itemprop="ingredients">';
					if ( $info[0] !== '' ) {
						$ingredientsList .= $info[0] . " ";	
					}
					
					if ( $info[1] !== "" ) {
						if ( $useSmallForm == 'true' ) {
							$info[1] = AbbreviateUnit( $info[1] );	
						}
						$ingredientsList .= $info[1] . " ";
					}
					$ingredientsList .= $info[2];
					$ingredientsList .= '</li>';	
				}
				
			}
			
			$ingredientsList .= '</ul>';
		}
    	
    	$html = str_replace( '{$ingredients}', $ingredientsList, $html );
    	
    	//Instructions
		if ( $recipeData->instructions !== "" ) {
	
			$instructionsList = '<ol class="instructions-list" itemprop="recipeInstructions">';
			
			$instructions = explode( '|||', $recipeData->instructions );
			foreach ( $instructions as $instruction ) {
				$info = explode( '||', $instruction );

				for ( $i = 0; $i < count( $info ); $i++ ) {
					if ( $info[$i] == " " ) {
						$info[$i] = "";	
					}	
				}
				
				if ( $info[0] == 'label' ) {
					$instructionsList .= '</ol>';
					$instructionsList .= '<div class="label-ingredients">' . $info[1] . '</div>';
					$instructionsList .= '<ol class="instructions-list" itemprop="recipeInstructions">';
				} else {
					$instructionsList .= '<li>' . $info[1] . '</li>';
				}
			}
			
			$instructionsList .= '</ol>';
		}
    	
    	$html = str_replace( '{$instructions}', $instructionsList, $html );
    	
    	//Note
    	$html = str_replace( '{$note}', $recipeData->note, $html );
    	
    	if ( GetOption( 'hideimage' ) == 'true' && !$isPrint ) {
    		$html .= '<script>HideRecipeImage();</script>';	
    	}
    	
    	if ( GetOption( 'displayrewrfooter' ) == 'true' && !$isPrint ) {
    		$html .= '<div class="rewr-smkfooter"><a class="smkfooter-text" href="http://smellmykitchen.com/recipes-writer">Powered by Recipes Writer</a></div>';
    	}
    	
    	return $html;
    }
}
add_shortcode( 'rewr', 'DisplayRecipe' );

//Load recipe template css
function loadRecipeTemplate()
{
	require_once( __DIR__ . '/functions.php' );
	$uploadDir = wp_upload_dir();
 	$templateDir = $uploadDir['baseurl'] . '/recipes-writer-templates';
	wp_enqueue_style( 'recipes-writer-template-css', $templateDir . '/' . GetOption( 'template' ) . '.css' );
}
add_action( 'wp_enqueue_scripts', 'loadRecipeTemplate', 99 );


function print_handler() {
	$url = strtok($_SERVER["REQUEST_URI"], '?');
	$urlArray = explode( '/', rtrim( $url, '/' ) );
	$lastIndex = count( $urlArray ) - 1;
	
   	if( $urlArray[ $lastIndex -1 ] == 'rewr-print' ) {
   		$recipeID = $urlArray[ $lastIndex ];
   		
   		//Load required files
   		require_once( __DIR__ . '/functions.php' );
   		
   		$data = array( 'id' => $recipeID );
   		$recipeData = GetRecipe( $recipeID );
   		$template = GetOption( 'template' );
   		
   		$printStyleExists = false;
   		$uploadDir = wp_upload_dir();
 		$templateDir = $uploadDir['basedir'] . '/recipes-writer-templates';
   		$files = scandir( $templateDir );
   		foreach ( $files as $file ) {
   			if ( $file == $template . '-print.php' ) {
   				$printStyleExists = true;	
   			}
   		}
   		if ( $printStyleExists ) {
   			$template = $template . '-print';	
   		}
   		$data['template'] = $template;
   		
   		$html .= '<!DOCTYPE html><html><head>';
   		
   		//Load the main stylesheet
   		$html .= '<link href="' . plugins_url( 'recipes-writer.css', __FILE__ ) . '" rel="stylesheet" type="text/css">';
   		
   		//Load the template stylesheet
   		$html .= '<link href="' . $uploadDir['baseurl'] . $template . '.css' . '" rel="stylesheet" type="text/css">';
   		
   		$html .= '<title>Print: ' . $recipeData->name . ' - ' . get_bloginfo( 'name' ) . '</title>';
   		$html .= '</head><body>';
   		
   		//The actual recipe
   		$html .= utf8_decode( DisplayRecipe( $data ) );
   		
   		//Load the .js external file
   		$html .= '<script src="' . plugins_url( 'recipes-writer.js', __FILE__ ) . '" type="text/javascript"></script>';
   		
   		//Load jQuery
   		$html .= '<script src="' . home_url() . '/wp-includes/js/jquery/jquery.js" type="text/javascript"></script>';
   		
   		//This will change all link to link title (url)
   		$html .= '<script>jQuery(document).ready( function() { PrintDisplayLinks(); });</script>';
   		$html .= '</body>';
   		$html .= '</html>';
   		echo $html;
   		
    	exit();
   	}
}
add_action('parse_request', 'print_handler');

// Rating in comment form
function add_comment_rating( $defaults ) {
	if ( GetOption( 'userating' ) == 'true' ) {
		global $rewrID;
		$html = "<div class='rewr-rating'>Rating: 
			<span class='rewr-rating-stars'>
			<input type='hidden' name='rewr-rating' value='0'>
			<input type='hidden' name='rewr-id' value='" . $rewrID . "'>
			<img class='rewr-rating-icon' data-rating='1' data-path='" . plugins_url( 'icons/', __FILE__ ) . "' src='" . plugins_url( 'icons/star-off.png', __FILE__ ) . "' alt='&star;' nopin='nopin'>
			<img class='rewr-rating-icon' data-rating='2' data-path='" . plugins_url( 'icons/', __FILE__ ) . "' src='" . plugins_url( 'icons/star-off.png', __FILE__ ) . "' alt='&star;' nopin='nopin'>
			<img class='rewr-rating-icon' data-rating='3' data-path='" . plugins_url( 'icons/', __FILE__ ) . "' src='" . plugins_url( 'icons/star-off.png', __FILE__ ) . "' alt='&star;' nopin='nopin'>
			<img class='rewr-rating-icon' data-rating='4' data-path='" . plugins_url( 'icons/', __FILE__ ) . "' src='" . plugins_url( 'icons/star-off.png', __FILE__ ) . "' alt='&star;' nopin='nopin'>
			<img class='rewr-rating-icon' data-rating='5' data-path='" . plugins_url( 'icons/', __FILE__ ) . "' src='" . plugins_url( 'icons/star-off.png', __FILE__ ) . "' alt='&star;' nopin='nopin'>
			</span></div>";
		$html .= "<script>preload('" . plugins_url( 'icons/star-on.png', __FILE__ ) . "');</script>";
		echo $html;
	}
}
add_action( 'comment_form_after_fields', 'add_comment_rating', 99 );

//Rating in submitted comment
function add_comment_rating_field( $comment_id ) {
	require_once( __DIR__ . '/functions.php' );
	
	if ( GetOption( 'userating' ) == 'true' ) {
		$rewrID = $_POST['rewr-id'];
		$IDs = explode( ',', $rewrID );
		foreach ( $IDs as $id ) {
			$ratingtotal = GetRatingTotal( $id );
			$ratingcount = GetRatingCount( $id );
			
			if ( $_POST['rewr-rating'] != 0 ) {
				add_comment_meta( $comment_id, 'rewr-rating', $_POST['rewr-rating'] );
				
				$ratingtotal += $_POST['rewr-rating'];
				$ratingcount++;
				
						
				$data = array(
					'id' => $id,
					'ratingtotal' => $ratingtotal,
					'ratingcount' => $ratingcount
				);
				
				UpdateRating( $data );
			}
		}
	}
}
add_action( 'comment_post', 'add_comment_rating_field' );

function insert_comment_rating( $comment_content ) {
	require_once( __DIR__ . '/functions.php' ); //We require this line so it doesn't break the Comments page in admin
	$ratingCount = get_comment_meta( get_comment_ID(), 'rewr-rating', true );
	if ( $ratingCount > 0 && GetOption( 'userating' ) == 'true' ) {
		$html = '<p>' . get_comment_author() . '\'s rating: ';
		
		for ( $i = 1; $i <= 5; $i++ ) {
			if ( $i <= $ratingCount ) {
				$html .= "<img class='rewr-rating-icon' src='" . plugins_url( 'icons/star-on.png', __FILE__ ) . "' alt='&star;' nopin='nopin'>";
			} else {
				$html .= "<img class='rewr-rating-icon' src='" . plugins_url( 'icons/star-off.png', __FILE__ ) . "' alt='&star;' nopin='nopin'>";
			}
		}
		$html .= '</p>';
		$html .= $comment_content;
		return $html;
	} else {
		return $comment_content;	
	}
}
add_filter( 'get_comment_text', 'insert_comment_rating' ); ?>