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

	$id = null;
	$mass = GetOption( 'mass' );
	$liquid = GetOption( 'liquid' );
	$temperature = GetOption( 'temperature' );
	$smallForm = GetOption( 'smallform' );
	
	$measurements = array();
	
	if ( $mass == 'metric' ) {
		array_push( $measurements, 'gram', 'grams', 'kilogram', 'kilograms' );
	} else if ( $mass == 'imperial' ) {
		array_push( $measurements, 'ounce', 'ounces', 'pound', 'pounds' );
	}
	
	if ( $liquid == 'metric' ) {
		array_push( $measurements, 'millilitre', 'millilitres', 'litre', 'litres' );
	} else if ( $liquid == 'customary' ) {
		array_push( $measurements, 'tablespoon', 'tablespoons', 'teaspoon', 'teaspoons', 'cup', 'cups' );
	}
	
	if ( $temperature == 'fahrenheit' ) {
		$temperatureIndicator = 'F';
	} else if ( $temperature == 'celcius' ) {
		$temperatureIndicator = 'C';
	}
	
	//We set the ID if it's in the url call
	if ( isset( $_GET['id'] ) && $_GET['id'] !== "" ) {
		$id = $_GET['id'];
	}

	if ( isset( $_POST['actionclicked'] ) ) {
		$action = $_POST['actionclicked'];
		//Create the string to save in the database for the ingredients
		$ingredientsCount = 0;
		foreach ( $_POST as $name => $val ) {
			if ( strpos( $name, 'ingredient-amount-' ) !== false ) {
				$ingredientIndex = str_replace( 'ingredient-amount-', '', $name );

				$amount = $_POST['ingredient-amount-' . $ingredientIndex];
				if ( $amount == "" ) {
					$amount = " ";
				}
				$measurement = $_POST['ingredient-measurement-' . $ingredientIndex];
				if ( $measurement == "" ) {
					$measurement = " ";	
				}
				$text = $_POST['ingredient-text-' . $ingredientIndex];
				if ( $text == "" ) {
					$text = " ";	
				}
				
				if ( $ingredientsCount > 0 ) {
					$ingredients .= '|||';
				}
				$ingredients .= $amount . '||' . $measurement . '||' . $text;
				$ingredientsCount++;
			}	
		}
		
		//Create the string to save in the database for the instructions
		$instructionsCount = 0;
		foreach ( $_POST as $name => $val ) {
			if ( strpos( $name, 'instruction-type-' ) !== false ) {
				$instructionIndex = str_replace( 'instruction-type-', '', $name );

				$type = $_POST['instruction-type-' . $instructionIndex];

				$text = $_POST['instruction-text-' . $instructionIndex];
				if ( $text == "" ) {
					$text = " ";	
				}
				
				if ( $instructionsCount > 0 ) {
					$instructions .= '|||';
				}
				$instructions .= $type . '||' . $text;
				$instructionsCount++;
			}	
		}
		
		if ( $action == 'save' || $action == 'saveandclose' ) { //We put the data for the recipe in an array
			$data = array (
				'id' => $id,
				'name' => stripslashes( $_POST['name'] ),
				'image' => stripslashes( esc_html( $_POST['image'] ) ),
				'imageid' => $_POST['imageid'],
				'categoryid' => stripslashes( esc_html( $_POST['category'] ) ),
				'cuisineid' => stripslashes( esc_html( $_POST['cuisine'] ) ),
				'description' => stripslashes( $_POST['description'] ),
				'author' => stripslashes( esc_html( $_POST['author'] ) ),
				'preparationtime' => stripslashes( esc_html( $_POST['preparationtime'] ) ),
				'cooktime' => stripslashes( esc_html( $_POST['cooktime'] ) ),
				'totaltime' => stripslashes( esc_html( $_POST['totaltime'] ) ),
				'servings' => stripslashes( esc_html( $_POST['servings'] ) ),
				'ingredients' => stripslashes( esc_html( $ingredients ) ),
				'instructions' => stripslashes( esc_html( $instructions ) ),
				'note' => stripslashes( $_POST['note'] )
			);
		}
		
		switch ( $action ) {
			case "save":
				$id = SaveRecipe( $data );
				if ( $id == false ) { // If false is returned it means the recipe was not saved.
					$id = null;
					echo '<script>ShowMessage("Error saving recipe. Make sure recipe\'s name is filled.", 5000, "error");</script>';	
				} else { // Else the recipe was saved.
					echo '<script>ShowMessage("Recipe saved successfully.");</script>';
				}
				break;
			case "saveandclose":
				$id = SaveRecipe( $data );
				if ( $id == false ) { // If fakse is returned it means the recipe was not saved.
					$id = null;
					echo '<script>ShowMessage("Error saving recipe. Make sure recipe\'s name is filled.", 5000, "error");</script>';	
				} else { // Else the recipe was saved.
					echo '<script>location.href="?page=rewr_recipes&highlight=' . $id . '";</script>';
				}
				break;
			default:
				break;
		}
	}

	// If the ID is null it means we are in a new recipe, else we are in a saved recipe
	if ( $id == null && $data == null ) {
		$recipe = null;
	} else {
		if ( $data !== null ) {
			$recipe = json_decode( json_encode( $data ), FALSE );
		} else {
			$recipe = GetRecipe( $id );
		}
	}
?>
<div id="editor">
	<form method="post" action="?page=rewr_recipes&action=edit&id=<?php 
		if ( $recipe->id !== null ) {
			echo $recipe->id;
		} else if ( $id !== null ) {
			echo $id;	
		}
		?>">
		<div class="clearfix">
			<div class="fixed-info">
				<label class="edit" for="id">ID:</label><input type="text" class="small" value="<?php
					if ( $recipe->id !== null ) {
						echo $recipe->id;
					} else if ( $id !== null ) {
						echo $id;	
					} 
					?>" readonly="readonly">
				<label class="edit" for="shortcode">Shortcode: </label><input class="shortcode-input small" type="text" value="[rewr id=&quot;<?php 
					if ( $recipe->id !== null ) {
						echo $recipe->id;
					} else if ( $id !== null ) {
						echo $id;	
					}
					?>&quot;]" readonly="readonly">
				<div class="alignright">
					<input type="hidden" name="actionclicked" value="">
					<input type="submit" class="button button-primary" data-action="save" value="Save Recipe">
					<input type="submit" class="button button-secondary" data-action="saveandclose"  value="Save & Close">
				</div>
			</div>
			<div class="inner">
				<div class="alignright">
					<?php 
						if ( $recipe->image !== null ) {
							echo wp_get_attachment_image( $recipe->imageid, 'rewr-image-thumb' );	
						}
					?>
				</div>
				<label class="edit" for="name">Recipe's Name:</label><input type="text" name="name" class="large" value="<?php echo $recipe->name; ?>">
				<br><br>
				<label class="edit" for="image">Image:</label><input type="text" class="imageuploadpath" id="recipe-image-textbox" value="<?php echo $recipe->image; ?>" name="image">&nbsp;<input type="button" class="upload_image_button" value="Browse..." data-target="#recipe-image-textbox">
				<input type="hidden" value="<?php echo $recipe->imageid; ?>" name="imageid">
				<br><br>
				<div class="col50-left">
					<label class="edit" for="category">Category:</label>
					<select name="category" class="medium">
						<option></option>
						<?php 
							$categories = GetCategoriesList();
							foreach ($categories as $category) {
								if ( $recipe->categoryid == $category->id ) {
									echo '<option value="' . $category->id . '" selected="selected">' . $category->category . '</option>';
								} else {
									echo '<option value="' . $category->id . '">' . $category->category . '</option>';
								}
							}
						?>
					</select>
				</div>
				<div class="col50-right"><label class="edit" for="servings">Servings:</label><input class="tiny" type="text" value="<?php echo $recipe->servings; ?>" name="servings"></div>
				<br><br>
				<div class="col50-left">
					<label class="edit" for="cuisine">Cuisine:</label>
					<select name="cuisine" class="medium">
						<option></option>
						<?php 
							$cuisines = GetCuisinesList();
							foreach ($cuisines as $cuisine) {
								if ( $recipe->cuisineid == $cuisine->id ) {
									echo '<option value="' . $cuisine->id . '" selected="selected">' . $cuisine->cuisine . '</option>';
								} else {
									echo '<option value="' . $cuisine->id . '">' . $cuisine->cuisine . '</option>';
								}
							}
						?>
					</select>
				</div>
				<div class="col50-right"><label class="edit" for="preparationtime">Prep Time: (minutes)&nbsp;</label><input class="tiny timeinput" type="text" value="<?php echo $recipe->preparationtime; ?>" name="preparationtime"></div>
				<br><br>
				<div class="col50-left"><label class="edit" for="author">Author:</label><input class="medium" type="text" value="<?php if ( $recipe->author === null ) { echo GetOption( 'defaultauthor' ); } else { echo $recipe->author; } ?>" name="author"></div>
				<div class="col50-right"><label class="edit" for="cooktime">Cook Time: (minutes)&nbsp;</label><input class="tiny timeinput" type="text" value="<?php echo $recipe->cooktime; ?>" name="cooktime"></div>
				<input class="tiny" type="hidden" value="<?php echo $recipe->totaltime; ?>" name="totaltime" readonly="readonly">
				<br><br>
				<label class="edit" for="description">Description:</label><textarea name="description" class="description"><?php echo $recipe->description; ?></textarea>
				<br><br>
				<label class="edit" for="note">Note:</label><textarea name="note" class="note"><?php echo $recipe->note; ?></textarea>
				<br><br>
				<div id="ingredients">
					<h3>Ingredients</h3>
					<div class="moreinfo">You can drag and drop ingredients and labels by clicking on the blue arrow at the end of the line.</div>
					<?php 
						//Display ingredients
						$ingredientIndex = 0;
						echo '<ul class="sortable">';
						if ( $recipe !== null && $recipe->ingredients !== "" ) {
							$ingredients = explode( '|||', $recipe->ingredients );
							foreach ( $ingredients as $ingredient ) {
								$measurementSelected = false;
								$info = explode( '||', $ingredient );
								for ( $i = 0; $i < count( $info ); $i++ ) {
									if ( $info[$i] == " " ) {
										$info[$i] = "";	
									}	
								}
								$input = '<li id="ingredient-' . $ingredientIndex . '"class="ingredient">';
	  							$input .= '	<input type="checkbox" class="ingredient-check" id="ingredient-check-' . $ingredientIndex . '">';
	  							if ( $info[0] == 'label' ) {
	  								$input .= ' <input type="hidden" ';
	  							} else {
	  								$input .= ' <input type="text" ';
	  							}
	  							$input .= 'class="tiny ingredient-amount" name="ingredient-amount-' . $ingredientIndex . '" value="' . $info[0] . '">';
	  							
	  							if ( $info[0] == 'label' ) {
	  								$input .= ' 	<input type="hidden" value="" class="small ingredient-measurement" name="ingredient-measurement-' . $ingredientIndex . '">';
	  								$input .= '<span class="label-image"></span>';
	  							} else {
		  							$input .= '<select name="ingredient-measurement-' . $ingredientIndex . '" class="small ingredient-measurement">';
									$input .= '<option></option>';
									foreach ( $measurements as $measurement ) {
										if ( $info[1] == $measurement ) {
											$measurementSelected = true;
											$input .= '<option value="' . $measurement . '" selected="selected">' . $measurement . '</option>';
										} else {
											$input .= '<option value="' . $measurement . '">' . $measurement . '</option>';
										}
									}
									
									if ( $measurementSelected == false && $info[1] !== "" ) {
										$input .= '<option value="' . $info[1] . '" selected="selected">' . $info[1] . '</option>';
									}
									$input .= '</select>';
	  							}
								
								if ( $info[0] == 'label' ) {
									$input .= '	<input type="text" class="label-text" name="ingredient-text-' . $ingredientIndex . '" value="' . $info[2] . '">';
								} else {
	  								$input .= '	<input type="text" class="ingredient-text" name="ingredient-text-' . $ingredientIndex . '" value="' . $info[2] . '">';
								}
								$input .= '<div class="sort-arrow"></div>';
	  							$input .= '</li>';
	  							echo $input;
	  							$ingredientIndex++;
							}
						}
						echo '</ul>';
						echo '<script> jQuery(document).ready( function() { CalculateNumIngredients(); });</script>';
						
						$ingredientMeasurementsInput .= '<option></option>';
						foreach ( $measurements as $measurement ) {
							$ingredientMeasurementsInput .= '<option value="' . $measurement . '">' . $measurement . '</option>';
						}
							
						echo '<script>SetIngredientMeasurementsInput("' . addslashes( $ingredientMeasurementsInput ) . '");</script>';
					?>
					<input type="hidden" name="numingredients" value="0">
				</div>
				<input type="button" class="button button-primary add-ingredient colleft" value="Add Ingredient"><input type="button" class="button button-secondary remove-ingredients" value="Remove Selected"><input type="button" class="button button-primary add-ingredient-label" value="Add Label">
				<br><br>
				<div id="instructions">
					<h3>Instructions</h3>
					<div class="moreinfo">You can drag and drop insctructions and labels by clicking on the blue arrow at the end of the line.</div>
					<?php 
						//Display ingredients
						$instructionIndex = 0;
						echo '<ul class="sortable">';
						if ( $recipe !== null && $recipe->instructions !== "" ) {
							$instructions = explode( '|||', $recipe->instructions );
							foreach ( $instructions as $instruction ) {
								$info = explode( '||', $instruction );
								$input = '<li id="instruction-' . $instructionIndex . '" class="instruction">';
	  							$input .= '	<input type="checkbox" class="instruction-check" id="instruction-check-' . $instructionIndex . '">';
	  							if ( $info[0] == 'label' ) {
	  								$input .= '<input type="hidden" name="instruction-type-' . $instructionIndex . '" value="label">';
	  								$input .= ' <span class="label-image"></span>';
	  								$input .= '	<input type="text" class="label-text" name="instruction-text-' . $instructionIndex . '" value="' . $info[1] . '">';
	  							} else {
	  								$input .= '<input type="hidden" name="instruction-type-' . $instructionIndex . '" value="ingredient">';
	  								$input .= '	<input type="text" class="instruction-text" name="instruction-text-' . $instructionIndex . '" value="' . $info[1] . '">';
	  							}
	  							$input .= '<div class="sort-arrow"></div>';
	  							$input .= '</li>';
	  							
	  							echo $input;
	  							$instructionIndex++;
							}
						}
						echo '</ul>';
						echo '<script> jQuery(document).ready( function() { CalculateNumInstructions(); });</script>';
					?>
					<input type="hidden" name="numinstructions" value="0">
				</div>
					<input type="hidden" name="temperatureindicator" value="<?php echo $temperatureIndicator; ?>">
					<input type="button" class="button button-primary add-instruction colleft" value="Add Instruction">
					<input type="button" class="button button-secondary remove-instructions" value="Remove Selected">
					<input type="button" class="button button-primary add-instruction-label" value="Add Label">
					<input type="button" class="button button-primary add-temperature-indicator alignright" value="Add Temperature Indicator (&deg;<?php echo $temperatureIndicator; ?>)">
					<div class="moreinfo colleft"><br>The "Add Temperature Indicator" button will add °C or °F at your cursor's location.</div>
			</div>
		</div>
		<div class="buttons clearfix">
			<div class="alignleft">
				<a href="?page=rewr_recipes" class="button button-secondary">Go back to Recipes List</a>
			</div>
			<div class="alignright">
				<input type="submit" class="button button-primary" data-action="save" value="Save Recipe">
				<input type="submit" class="button button-secondary" data-action="saveandclose"  value="Save & Close">
			</div>
		</div>
		<script>
			jQuery( document ).ready( function() {
				SetSortable();
			});
		</script>
	</form>
</div>