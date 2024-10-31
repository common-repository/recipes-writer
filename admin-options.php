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

?>

<?php 
	$message = "";
	
	if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
		//We save the categories to the database if we are in POST
		$categories = array();
		foreach ( $_POST as $name => $val ) {
			if ( strpos( $name, 'category-' ) !== false ) {
				$categoryID = str_replace( 'category-', '', $name );
				if ( substr( $categoryID, 0, 4 ) == "temp" ) {
					$categoryID = null;	
				}

				$data = array(
					'id'=>$categoryID,
					'category'=>$val
				);
				array_push( $categories, $data );
			}	
		}
		
		$success = SaveCategories( $categories );
			
		if ( $success === false ) {
			$message = "Error saving the categories. ";	
		}

		
		//We save the cuisines to the database if we are in POST
		$cuisines = array();
		foreach ( $_POST as $name => $val ) {
			if ( strpos( $name, 'cuisine-' ) !== false ) {
				$cuisineID = str_replace( 'cuisine-', '', $name );
				if ( substr( $cuisineID, 0, 4 ) == "temp" ) {
					$cuisineID = null;	
				}

				$data = array(
					'id'=>$cuisineID,
					'cuisine'=>$val
				);
				array_push( $cuisines, $data );
			}	
		}
		
		$success = SaveCuisines( $cuisines );
			
		if ( $success === false ) {
			$message .= "Error saving the cuisines. ";	
		}
		
		//Abbreviation option
		if ( $_POST['smallform'] == 'on' ) {
			$smallformOption = 'true';	
		} else {
			$smallformOption = 'false';	
		}
		$data = array(
			'optionname'=>'smallform',
			'data'=>$smallformOption
		);
			
		$success = SaveOption($data);
		
		if ( $success === false ) {
			$message .= "Error saving the abbreviation option. ";	
		}
				
		//Mass option
		$data = array(
			'optionname'=>'mass',
			'data'=>$_POST['mass']
			);
			
		$success = SaveOption($data);
		
		if ( $success === false ) {
			$message .= "Error saving the mass option. ";	
		}
		
		//Liquid option
		$data = array(
			'optionname'=>'liquid',
			'data'=>$_POST['liquid']
			);
			
		$success = SaveOption($data);
		
		if ( $success === false ) {
			$message .= "Error saving the liquid option. ";	
		}
		
		//Temperature option
		$data = array(
			'optionname'=>'temperature',
			'data'=>$_POST['temperature']
			);
			
		$success = SaveOption($data);
		
		if ( $success === false ) {
			$message .= "Error saving the temperature option. ";	
		}
		
		//Template option
		$templateName = $_POST['template'];
		
		$data = array(
			'optionname'=>'template',
			'data'=>$templateName
			);
			
		$success = SaveOption($data);
		
		if ( $success === false ) {
			$message .= "Error saving the template option. ";	
		}
		
		//Template option
		$defaultAuthor = $_POST['defaultauthor'];
		
		$data = array(
			'optionname'=>'defaultauthor',
			'data'=>$defaultAuthor
			);
			
		$success = SaveOption($data);
		
		if ( $success === false ) {
			$message .= "Error saving the default author option. ";	
		}
		
		//Hide image option
		if ( $_POST['hideimage'] == 'on' ) {
			$hideImageOption = 'true';	
		} else {
			$hideImageOption = 'false';	
		}
		$data = array(
			'optionname'=>'hideimage',
			'data'=>$hideImageOption
			);
			
		$success = SaveOption($data);
		
		if ( $success === false ) {
			$message .= "Error saving the hide image option. ";	
		}
		
		//Template option
		$printButtonLabel = $_POST['printbuttonlabel'];
		
		$data = array(
			'optionname'=>'printbuttonlabel',
			'data'=>$printButtonLabel
			);
			
		$success = SaveOption($data);
		
		if ( $success === false ) {
			$message .= "Error saving the print button label option. ";	
		}
		
		//Use rating option
		if ( $_POST['userating'] == 'on' ) {
			$useRating = 'true';	
		} else {
			$useRating = 'false';	
		}
		$data = array(
			'optionname'=>'userating',
			'data'=>$useRating
			);
			
		$success = SaveOption($data);
		
		if ( $success === false ) {
			$message .= "Error saving the use rating option. ";	
		}
		
		//Show review count option
		if ( $_POST['showreviewcount'] == 'on' ) {
			$showReviewCount = 'true';	
		} else {
			$showReviewCount = 'false';	
		}
		$data = array(
			'optionname'=>'showreviewcount',
			'data'=>$showReviewCount
			);
			
		$success = SaveOption($data);
		
		if ( $success === false ) {
			$message .= "Error saving the show review count option. ";	
		}
		
		//Display MSK footer option
		if ( $_POST['displayrewrfooter'] == 'on' ) {
			$displayRewrFooter = 'true';	
		} else {
			$displayRewrFooter = 'false';	
		}
		$data = array(
			'optionname'=>'displayrewrfooter',
			'data'=>$displayRewrFooter
			);
			
		$success = SaveOption($data);

		if ( $success === false ) {
			$message .= "Error saving the \"support the developper\" option. ";	
		}
	
		if ( $message !== "" ) {
			echo '<script>jQuery(document).ready( function() { ShowMessage("' . addslashes( $message ) . '", 1500, "error"); });</script>';
		} else {
			echo '<script>jQuery(document).ready( function() { ShowMessage("Options saved successfully."); });</script>';
		}
	}
?>

<div class="wrap rewr rewr-options">
	<h1>Recipes Writer - Options</h1>
	<p id="message">No message</p>
	<form method="post">
	<h2>Measurements</h2>
	<div class="moreinfo">Changes made to the measurements options will affect future recipes only. Recipes Writer won't make any conversions.</div>
	<?php
		$mass = GetOption( 'mass' );
		$liquid = GetOption( 'liquid' );
		$temperature = GetOption( 'temperature' );
		$smallform = GetOption( 'smallform' );
		$hideimage = GetOption( 'hideimage' );
		$userating = GetOption( 'userating' );
		$showreviewcount = GetOption( 'showreviewcount' );
		$displayrewrfooter = GetOption( 'displayrewrfooter' );
		$defaultAuthor = GetOption( 'defaultauthor' );
		$printButtonLabel = GetOption( 'printbuttonlabel' );
	?>
	<label class="longlabel"><input type="checkbox" name="smallform" <?php if ( $smallform == 'true' ) echo 'checked'; ?>>Use units abbreviations when displaying recipes</label>
	<table>
		<tbody>
			<tr>
				<td class="col-medium"><label for="mass">Mass units:</label></td>
				<td>
					<input type="radio" name="mass" value="imperial" <?php if ( $mass == 'imperial' ) { echo 'checked'; } ?>>Imperial (ounces and pounds)</input>
					<br>
					<input type="radio" name="mass" value="metric" <?php if ( $mass == 'metric' ) { echo 'checked'; } ?>>Metric (grams and kilograms)</input>
				</td>
			</tr>
			<tr>
				<td class="col-medium"><label for="liquid">Liquid units:</label></td>
				<td>
					<input type="radio" name="liquid" value="customary" <?php if ( $liquid == 'customary' ) { echo 'checked'; } ?>>Customary (tablespoons, teaspoons and cups)</input>
					<br>
					<input type="radio" name="liquid" value="metric" <?php if ( $liquid == 'metric' ) { echo 'checked'; } ?>>Metric (millilitres and litres)</input>
				</td>
			</tr>
			<tr>
				<td class="col-medium"><label for="temperature">Temperature:</label></td>
				<td>
					<input type="radio" name="temperature" value="fahrenheit" <?php if ( $temperature == 'fahrenheit' ) { echo 'checked'; } ?>>Fahrenheit</input>
					<br>
					<input type="radio" name="temperature" value="celcius" <?php if ( $temperature == 'celcius' ) { echo 'checked'; } ?>>Celcius</input>
				</td>
			</tr>
		</tbody>
	</table>
	<div class="clearfix"><p class="alignright"><input type="submit" class="button button-primary" value="Save Options"></p></div>
	<!-- Categories -->
	<h2>Categories</h2>
	<div class="moreinfo">
		Modifying or deleting a category will affect all recipes using it.<br>
		After deleting a category, you need to click "Save Options" for it to take effect.
	</div>
	<div class="menu-top clearfix">
		<ul class="menu-left">
			<li><a href="javascript:void();" class="selectall" data-table="categories-list">Select All</a></li>
			<li><a href="javascript:void();" class="deselectall" data-table="categories-list">Deselect All</a></li>
			<li><a href="javascript:void();" class="deletecategories" data-table="categories-list">Delete Selected</a></li>
		</ul>
		<ul class="menu-right">
			<li><a href="javascript:void();" class="addcategory">Add New Category</a></li>
		</ul>
	</div>
	<table id="categories-list">
		<thead>
			<tr>
				<th scope="col" class="col-small"></th>
				<th scope="col">Category Name</th>
			</tr>
		</thead>
		<tbody>
			<?php
				$categories = GetCategoriesList();
				foreach ( $categories as $category ) {
					echo '<tr class="category">';
					echo '<td><input type="checkbox" class="selectbox"></td>';
					echo '<td><input type="text" class="stretch category-text" name="category-' . $category->id . '" value="' . $category->category . '" ></td>';
					echo '</tr>';
				}
			?>
		</tbody>
	</table>
	<!-- End Categories -->
	<div class="clearfix"><p class="alignright"><input type="submit" class="button button-primary" value="Save Options"></p></div>
	<!-- Cuisine -->
	<h2>Cuisines</h2>
	<div class="moreinfo">
		Modifying or deleting a cuisine will affect all recipes using it.<br>
		After deleting a cuisine, you need to click "Save Options" for it to take effect.
	</div>
	<div class="menu-top clearfix">
		<ul class="menu-left">
			<li><a href="javascript:void();" class="selectall" data-table="cuisines-list">Select All</a></li>
			<li><a href="javascript:void();" class="deselectall" data-table="cuisines-list">Deselect All</a></li>
			<li><a href="javascript:void();" class="deletecuisines" data-table="cuisines-list">Delete Selected</a></li>
		</ul>
		<ul class="menu-right">
			<li><a href="javascript:void();" class="addcuisine">Add New Cuisine Type</a></li>
		</ul>
	</div>
	<table id="cuisines-list">
		<thead>
			<tr>
				<th scope="col" class="col-small"></th>
				<th scope="col">Cuisine Name</th>
			</tr>
		</thead>
		<tbody>
			<?php
				$cuisines = GetCuisinesList();
				foreach ( $cuisines as $cuisine ) {
					echo '<tr class="cuisine">';
					echo '<td><input type="checkbox" class="selectbox"></td>';
					echo '<td><input type="text" class="stretch cuisine-text" name="cuisine-' . $cuisine->id . '" value="' . $cuisine->cuisine . '" ></td>';
					echo '</tr>';
				}
			?>
		</tbody>
	</table>
	<!-- End Cuisines -->
	<div class="clearfix"><p class="alignright"><input type="submit" class="button button-primary" value="Save Options"></p></div>
	<!-- Template -->
	<h2>Other Options</h2>
	<h3>Template</h3>
	Select the template for your recipes:&nbsp;
	<select name="template">
		<?php 
			$templates = GetTemplates(); 
			$currentTemplate = GetOption( 'template' );
			
			foreach ( $templates as $template ) {
				if ( strpos( $template, '-print' ) == false ) {
					if ( $template == $currentTemplate ) {
						echo '<option name="' . $template . '" selected="selected">' . $template . '</option>';
					} else {
						echo '<option name="' . $template . '">' . $template . '</option>';
					}
				}
			}
		?>
	</select>
	<div class="moreinfo">
		<?php
			$uploadDir = wp_upload_dir();
	 		$templateDir = $uploadDir['basedir'] . '/recipes-writer-templates';
	 	?>
		Templates are located in <?php echo $templateDir; ?>/.<br>
		To create your own templates, simply <a href="http://smellmykitchen.com/recipes-writer-faq/#createtemplate" target="_blank">follow our guide</a>.
	</div>
	<!-- End Template -->
	
	<!-- Default author -->
	<h3>Default Author</h3>
	Default recipes' author:&nbsp;
	<input type="textbox" class="medium" name="defaultauthor" value="<?php echo $defaultAuthor; ?>">
	<div class="moreinfo">This is the name that will appear by default in the author field.</div>
	<!-- End default author -->
	
	<!-- Hide image in recipe -->
	<h3>Recipe Image</h3>
	<label class="longlabel"><input type="checkbox" name="hideimage" <?php if ( $hideimage == 'true' ) echo 'checked'; ?>> Hide image in recipe</label>
	<div class="moreinfo">
		Recipes Writer will hide the recipe's image when displaying the recipe in your blog post.<br>
		This doesn't affect printing the printing template and it doesn't affect <a href="https://support.google.com/webmasters/answer/2722261?hl=en">Google Rich Snippets</a>.
	</div>
	
	<!-- Hide image in recipe -->
	<h3>Print Button</h3>
	Print button label:&nbsp;
	<input type="textbox" class="medium" name="printbuttonlabel" value="<?php echo $printButtonLabel; ?>">
	<div class="moreinfo">
		Text located besides the print icon.
	</div>
	<!-- Use rating -->
	<h3>Rating</h3>
	<label class="longlabel"><input type="checkbox" name="userating" <?php if ( $userating == 'true' ) echo 'checked'; ?>> Use rating system</label>
	<div class="moreinfo">
		Recipes Writer will display stars in the comment form and users will be able to rate your recipe.<br>
		Your WordPress theme most use the default comment_form() function to display the comment form.<br>
		Your recipe template requires {$rating} as a variable. This also means <a href="https://support.google.com/webmasters/answer/2722261?hl=en">Google Rich Snippets</a> will display stars besides the search result.
	</div>
	<!-- Show review count -->
	<h3>Review Count</h3>
	<label class="longlabel"><input type="checkbox" name="showreviewcount" <?php if ( $showreviewcount == 'true' ) echo 'checked'; ?>> Show review count in recipe</label>
	<div class="moreinfo">Recipes Writer will display the number of stars and add "based on # reviews" at the end. This is purely cosmetic.</div>
	<!-- Support plugin author -->
	<h3>Support the plugin (thank you!)</h3>
	<label class="longlabel"><input type="checkbox" name="displayrewrfooter" <?php if ( $displayrewrfooter == 'true' ) echo 'checked'; ?>> Support the plugin</label>
	<div class="moreinfo">Recipes Writer will display a link to the plugin's homepage below your recipes. We really appreciate it!</div>
	<p><input type="submit" class="button button-primary" value="Save Options"></p>
	</form>
</div>