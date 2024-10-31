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

//Variables for the Add Temperature Indicator
var selectedInstruction = null;
var cursorPosition = null;

(function($) {
    $(document).ready(function() {
	   	//Select all link to select all check boxes
    	$( ".selectall" ).each( function() {
    		$(this).click( function() {
    			var tableID = $(this).attr( 'data-table' );
    			$( '#' + tableID ).find( '.selectbox' ).each( function() {
    				$(this).attr( 'checked', 'checked' );	
    			});	
    		});	
    	});
    	
    	//Deselect all link to deselect all check boxes
    	$( ".deselectall" ).each( function() {
    		$(this).click( function() {
    			var tableID = $(this).attr( 'data-table' );
    			$( '#' + tableID ).find( '.selectbox' ).each( function() {
    				$(this).attr( 'checked', false );	
    			});	
    		});	
    	});
    	
    	//Send delete selected action
    	$( ".deleterecipes" ).each( function() {
    		$(this).click( function() {
    			var checkedRecipes = [];
    			var tableID = $(this).attr( 'data-table' );
    			$( '#' + tableID ).find( '.selectbox' ).each( function() {
    				if ( $(this).attr( 'checked' ) ) {
    					checkedRecipes.push( $(this).attr( 'data-recipe-id' ) );
    				}	
    			});
    			
    			if ( checkedRecipes.length > 0 ) {
    				window.location.href = "?page=rewr_recipes&action=delete&ids=" + checkedRecipes;
    			}
    		});	
    	});
    	
    	//Select text when entering shortcode textbox
    	$( '.shortcode-input' ).each( function() {
    		$( this ).focus( function() {
				$( this ).select();
			});
			
			$( this ).mouseup( function(e) {
				e.preventDefault();	
			});
    	});
    	
    	//Submit buttons for editor
    	$( '#editor input[type="submit"]' ).each( function() {
    		$(this).click( function() {
    			$( '#editor input[name="actionclicked"]' ).attr( 'value', $(this).attr( 'data-action' ) );
    		});
    	});
    	
    	//Upload box 
  		$( '.upload_image_button' ).click( function() {
  			var target = $(this).attr('data-target');
  			
		    // Create the media frame.
		    var frame = wp.media.frames.file_frame = wp.media({
		      title: jQuery( this ).data( 'uploader_title' ),
		      button: {
		        text: jQuery( this ).data( 'uploader_button_text' ),
		      },
		      multiple: false  // Set to true to allow multiple files to be selected
		    });
		 
		    // When an image is selected, run a callback.
		    frame.on( 'select', function() {
		      // We set multiple to false so only get one image from the uploader
		      attachment = frame.state().get('selection').first().toJSON();
		 
		      // Do something with attachment.id and/or attachment.url here
		      $( target ).attr( 'value', attachment.url );
		      $( 'input[name="imageid"]' ).attr( 'value', attachment.id );
		    });
		 
		    // Finally, open the modal
		    frame.open();
  		});
  		
  		//Calculate total time
  		$( '.timeinput' ).focusout( function() {
  			var totaltime = 0;

  			if ( isNaN( parseInt( $( this ).attr( 'value' ) ) ) ) {
  				$( this ).attr( 'value', 0 );	
  			}
  			
  			$( '.timeinput' ).each( function() {
  				totaltime += parseInt( $( this ).attr( 'value' ) );
  			});
  			
  			$ ( 'input[name="totaltime"]' ).attr( 'value', totaltime );
  		});
  		
  		//Add ingredient
  		var ingredientIndex = 0;
  		$( '.ingredient' ).each( function() {
  			$( this ).attr( 'id', 'ingredient-' + ingredientIndex );
  			ingredientIndex++;
  		});
  		
  		$( '.add-ingredient' ).click( function() {
  			//Add the ingredient
  			var input = '<li id="ingredient-' + ingredientIndex + '" class="ingredient">';
  			input += '	<input type="checkbox" class="ingredient-check" id="ingredient-check-' + ingredientIndex + '">';
  			input += '	<input type="text" value="" class="tiny ingredient-amount" name="ingredient-amount-' + ingredientIndex + '">';
  			input += '<select name="ingredient-measurement-' + ingredientIndex + '" class="small ingredient-measurement">';
  			input += GetIngredientMeasurementsInput();
  			input += '</select>';
  			input += '	<input type="text" value="" class="ingredient-text" name="ingredient-text-' + ingredientIndex + '">';
  			input += '<div class="sort-arrow"></div></li>';
  			
  			$( '#ingredients ul' ).append( input );	
  			ingredientIndex++;
  			
  			CalculateNumIngredients();
  			SetSortable();
  		});
  		
  		$( '.remove-ingredients' ).click( function() {
  			$( '.ingredient' ).each( function() {
  				if ( $( this ).children( '.ingredient-check' ).attr( 'checked' ) ) {
  					$( this ).remove();
  				}	
  			});
  			
  			//Reset all the ids and the ingredientIndex
  			//We do this because when we send the post, we will know exactly which index we have, from 0 to numingredients
  			ingredientIndex = 0;
  			$( '.ingredient' ).each( function() {
  				$( this ).attr( 'id', 'ingredient-' + ingredientIndex );
  				$( this ).children( '.ingredient-check' ).attr( 'id', 'ingredient-check-' + ingredientIndex );
  				$( this ).children( '.ingredient-amount' ).attr( 'name', 'ingredient-amount-' + ingredientIndex );
  				$( this ).children( '.ingredient-measurement' ).attr( 'name', 'ingredient-measurement-' + ingredientIndex );
  				$( this ).children( '.ingredient-text' ).attr( 'name', 'ingredient-text-' + ingredientIndex );
  				ingredientIndex++;
  			});
  			
  			CalculateNumIngredients();
  		});
  		
  		$( '.add-ingredient-label' ).click( function() {
  			//Add label to the ingredients list
  			var input = '<li id="ingredient-' + ingredientIndex + '" class="ingredient">';
  			input += '	<input type="checkbox" class="ingredient-check" id="ingredient-check-' + ingredientIndex + '">';
  			input += '	<input type="hidden" value="label" class="tiny ingredient-amount" name="ingredient-amount-' + ingredientIndex + '">';
  			input += '<span class="label-image"></span>';
  			input += '	<input type="hidden" value="" class="small ingredient-measurement" name="ingredient-measurement-' + ingredientIndex + '">';
  			input += '	<input type="text" value="" class="label-text" name="ingredient-text-' + ingredientIndex + '">';
  			input += '<div class="sort-arrow"></div></li>';
  			
  			$( '#ingredients ul' ).append( input );	
  			ingredientIndex++;
  			
  			CalculateNumIngredients();
  			SetSortable();
  		});
  		
  		//Add instruction
  		var instructionIndex = 0;
  		$( '.instruction' ).each( function() {
  			$( this ).attr( 'id', 'instruction-' + instructionIndex );
  			instructionIndex++;
  		});
  		
  		$( '.add-instruction' ).click( function() {
  			//Add the instruction
  			var input = '<li id="instruction-' + instructionIndex + '" class="instruction">';
  			input += '	<input type="checkbox" class="instruction-check" id="instruction-check-' + instructionIndex + '">';
  			input += '	<input type="hidden" name="instruction-type-' + instructionIndex + '" value="ingredient">';
  			input += '	<input type="text" value="" class="instruction-text" name="instruction-text-' + instructionIndex + '">';
  			input += '<div class="sort-arrow"></div></li>';
  			
  			$( '#instructions ul' ).append( input );	
  			instructionIndex++;
  			
  			CalculateNumInstructions();
  			SetSortable();
  			
  			$( '.instruction-text' ).each( function() {
  				$( this ).unbind( 'click' );	
  			});
  			$( '.instruction-text' ).click( function() {
  				selectedInstruction = $( this );
  			});
  		});
  		
  		$( '.add-instruction-label' ).click( function() {
  			//Add the instruction
  			var input = '<li id="instruction-' + instructionIndex + '" class="instruction">';
  			input += '	<input type="checkbox" class="instruction-check" id="instruction-check-' + instructionIndex + '">';
  			input += '	<input type="hidden" name="instruction-type-' + instructionIndex + '" value="label">';
  			input += '<span class="label-image"></span>';
  			input += '	<input type="text" value="" class="label-text" name="instruction-text-' + instructionIndex + '">';
  			input += '<div class="sort-arrow"></div></li>';
  			
  			$( '#instructions ul' ).append( input );	
  			instructionIndex++;
  			
  			CalculateNumInstructions();
  			SetSortable();
  			
  			$( '.label-text' ).each( function() {
  				$( this ).unbind( 'click' );	
  			});
  			$( '.label-text' ).click( function() {
  				selectedInstruction = $( this );
  			});
  		});
  		
  		$( '.remove-instructions' ).click( function() {
  			$( '.instruction' ).each( function() {
  				if ( $( this ).children( '.instruction-check' ).attr( 'checked' ) ) {
  					$( this ).remove();
  				}	
  			});
  			
  			//Reset all the ids and the ingredientIndex
  			//We do this because when we send the post, we will know exactly which index we have, from 0 to numingredients
  			instructionIndex = 0;
  			$( '.instruction' ).each( function() {
  				$( this ).attr( 'id', 'instruction-' + instructionIndex );
  				$( this ).children( '.instruction-check' ).attr( 'id', 'instruction-check-' + instructionIndex );
  				$( this ).children( '.instruction-text' ).attr( 'name', 'instruction-text-' + instructionIndex );
  				instructionIndex++;
  			});
  			
  			CalculateNumInstructions();
  		});
  		
  		//Categories table
  		var categoryIndex = 1;
  		//Add new category to the list of categories
  		$( '.addcategory' ).each( function() {
  			$( this ).click( function() {
  				var form = '<tr class="category">';
  				form += '<td><input type="checkbox" class="selectbox"></td>';
  				form += '<td><input type="text" class="stretch category-text" name="category-temp' + categoryIndex + '" value="" ></td>';
  				form += '</tr>';
  				$( '#categories-list' ).find( 'tbody' ).append( form );
  				categoryIndex++;
  			});		
  		});
  		
  		//Delete the selected categories from the list
  		$( '.deletecategories').click( function() {
  				$( '.category' ).each( function() {
  					if ( $( this ).find( '.selectbox' ).attr( 'checked' ) ) {
  						$( this ).hide();
  						$( this ).find( '.category-text' ).attr( 'value', '' );
  					}
  				});
  		});
  		
  		//Cuisines table
  		var cuisineIndex = 1;
  		//Add new cuisine to the list of categories
  		$( '.addcuisine' ).each( function() {
  			$( this ).click( function() {
  				var form = '<tr class="cuisine">';
  				form += '<td><input type="checkbox" class="selectbox"></td>';
  				form += '<td><input type="text" class="stretch cuisine-text" name="cuisine-temp' + cuisineIndex + '" value="" ></td>';
  				form += '</tr>';
  				$( '#cuisines-list' ).find( 'tbody' ).append( form );
  				cuisineIndex++;
  			});		
  		});
  		
  		//Delete the selected cuisines from the list
  		$( '.deletecuisines').click( function() {
  				$( '.cuisine' ).each( function() {
  					if ( $( this ).find( '.selectbox' ).attr( 'checked' ) ) {
  						$( this ).hide();
  						$( this ).find( '.cuisine-text' ).attr( 'value', '' );
  					}
  				});
  		});
  		
  		//Bind the instructions and labels for temperature indicator
  		TemperatureIndicatorBind();
  		
  		
  		//Add the temperature indicator when clicked
  		$( '.add-temperature-indicator' ).click( function() {
  			if ( selectedInstruction !== null ) {
  				var value = [selectedInstruction.val().slice(0, cursorPosition), '°' + $( 'input[name=temperatureindicator]' ).val(), selectedInstruction.val().slice(cursorPosition)].join('');
  				selectedInstruction.val( value );
  				selectedInstruction.focus();
  				selectedInstruction.selectRange(cursorPosition + 2); //We use this to put the cursor back into position (after the temperature indicator)
  			}
  		});
  		
  		//Add the hover actions on the rating system
  		$( '.rewr-rating-stars .rewr-rating-icon' ).hover( function() {
  			var ratingIndex = $( this ).attr( 'data-rating' );

  			$( '.rewr-rating-stars .rewr-rating-icon' ).each( function() {
  				if ( parseInt( $( this ).attr( 'data-rating' ) ) <= ratingIndex ) {
  					$( this ).attr( 'src', $( this ).attr( 'data-path' ) + 'star-on.png' );	
  				} else {
  					$( this ).attr( 'src', $( this ).attr( 'data-path' ) + 'star-off.png' );
  				}
  			});
  		}, function() {
  			return;
  		});
  		
  		$( '.rewr-rating-stars .rewr-rating-icon' ).click( function() {
  			$( 'input[name=rewr-rating]' ).val( $( this ).attr( 'data-rating' ) );
  		});
  		
  		$( '.rewr-rating-stars' ).mouseleave( function() {
  			var ratingCount = parseInt( $( 'input[name=rewr-rating]' ).val() );
  			
  			$( '.rewr-rating-stars .rewr-rating-icon' ).each( function() {
  				if ( parseInt( $( this ).attr( 'data-rating' ) ) <= ratingCount ) {
  					$( this ).attr( 'src', $( this ).attr( 'data-path' ) + 'star-on.png' );	
  				} else {
  					$( this ).attr( 'src', $( this ).attr( 'data-path' ) + 'star-off.png' );
  				}
  			});
  		});
  		
  		$( '.rewr-recipe .print-button' ).each( function() {
  			var printHref = $( '.rewr-recipe' ).attr( 'data-print-href' );
  			$( this ).attr( 'href', printHref );
  		});
  		
  		$( '#editor' ).keyup( function() {
  			window.onbeforeunload = null;
  			window.onbeforeunload = confirmOnPageExit;
  		});
  		
  		$( 'input[data-action=save]' ).click( function() {
  			window.onbeforeunload = null;
  		});
  		
  		$( 'input[data-action=saveandclose]' ).click( function() {
  			window.onbeforeunload = null;
  		});
  		
  		/*// Turn it on - assign the function that returns the string
		window.onbeforeunload = confirmOnPageExit;*/
		
		/*// Turn it off - remove the function entirely
		window.onbeforeunload = null;*/
    });
})(jQuery);

//Preload the stars for the rating system (called from PHP)
function preload(image) {
   	$('<img/>')[0].src = image;
}

function HideRecipeImage() {
	$( '.rewr-recipe .recipe-image' ).hide();	
}

//Display a message at the top of the admin panel
function ShowMessage( message, fadeTime, color ) {
    $ = jQuery;
    
    if ( fadeTime === undefined ) {
    	fadeTime = 'slow';	
    }
    
    if ( color == 'error' )
    {
    	color = '#F78181';
    } else {
    	color = '#d7f5d6';
    }
    
    $( '#message' ).text( message ).css( {'opacity':'100', 'background-color':color} ).delay( 2000 ).fadeTo( fadeTime, 0 );
}

//Calculate the number of ingredients to update the hidden field
function CalculateNumIngredients() {
  	var numIngredients = $( '.ingredient' ).length;
  	$( 'input[name="numingredients"]' ).attr( 'value', numIngredients );	
}

//Calculate the number of instructions to update the hidden field
function CalculateNumInstructions() {
	var numInstructions = $( '.instruction' ).length;
  	$( 'input[name="numinstructions"]' ).attr( 'value', numInstructions );	
}

function PrintDisplayLinks() {
	$ = jQuery; //Required to use $ instead of jQuery
	$( 'a' ).each( function() {
		var href = $( this ).attr( 'href' );
		$( '<span>&nbsp;(' + href + ')</span>' ).insertAfter( $( this ) );
	});
}

var ingredientMeasurementsInput;
function GetIngredientMeasurementsInput() {
	return ingredientMeasurementsInput;	
}

function SetIngredientMeasurementsInput( input ) {
	ingredientMeasurementsInput = input;
}

function SetSortable() {
	$( ".sortable" ).sortable({
			sort: function() {
				window.onbeforeunload = null;
  				window.onbeforeunload = confirmOnPageExit;
			}
		});
}

var confirmOnPageExit = function (e) 
{
    // If we haven't been passed the event get the window.event
    e = e || window.event;

    var message = 'Any changes made to this recipe will be lost.';

    // For IE6-8 and Firefox prior to version 4
    if (e) 
    {
        e.returnValue = message;
    }

    // For Chrome, Safari, IE8+ and Opera 12+
    return message;
};

$.fn.selectRange = function(start, end) {
    if(!end) end = start; 
    return this.each(function() {
        if (this.setSelectionRange) {
            this.focus();
            this.setSelectionRange(start, end);
        } else if (this.createTextRange) {
            var range = this.createTextRange();
            range.collapse(true);
            range.moveEnd('character', end);
            range.moveStart('character', start);
            range.select();
        }
    });
};

function TemperatureIndicatorBind() {
	$( '.instruction-text' ).unbind();
	$( '.label-text' ).unbind();
	$( '.instruction-text' ).bind( 'keyup click', function() {
	  	selectedInstruction = $( this );
	  	cursorPosition = $(this).caret().start;
	});
  		
  	$( '.label-text' ).bind("keyup click", function() {
	  	selectedInstruction = $( this );
	  	cursorPosition = $(this).caret().start;
	});
};