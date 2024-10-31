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

<div class="wrap rewr rewr-list">
	<?php 
	//If the action is delete, delete the recipes in question
	if ( $_GET['action'] == 'delete' ) {
		$recipesToDelete = explode( ',', $_GET['ids'] );
		
		foreach ($recipesToDelete as $id) {
			DeleteRecipe( $id );
		}
		
		echo '<script type="text/javascript">
		$(document).ready( function() {
		ShowMessage( "Recipe(s) deleted successfully." );
		});
		</script>';
	}
	
	//Display the recipe list if there is no action
	if ( $_GET['action'] !== 'view' && $_GET['action'] !== "edit" ) :
	?>
	<h1>Recipes Writer - Recipes List</h1>
	<div class="moreinfo">To display recipes, just copy/paste the code in the "shortcode" column into your WordPress post. The plugin will transform the shortcode to the proper template with the proper recipe's information.</div>
	<p id="message">No message</p>
	<div id="searchform">
		<form method="get"><input type="hidden" name="page" value="rewr_recipes"><input type="text" name="search" placeholder="Search..."><input class="button button-secondary" type="submit" value="GO"></form>
	</div>	
	<div class="menu-top clearfix">
		<ul class="menu-left">
			<li><a href="javascript:void();" class="selectall" data-table="recipes-list">Select All</a></li>
			<li><a href="javascript:void();" class="deselectall" data-table="recipes-list">Deselect All</a></li>
			<li><a href="javascript:void();" class="deleterecipes" data-table="recipes-list">Delete Selected</a></li>
		</ul>
		<ul class="menu-right">
			<li><a href="?page=<?php echo $_GET['page']; ?>&action=edit">Create New Recipe</a></li>
		</ul>
	</div>
	<table id="recipes-list">
		<thead>
			<tr>
				<th scope="col" class="col-small"></td>
				<th scope="col" class="col-small"><a href="?page=<?php echo $_GET['page']; ?>&sort=id">ID</a><?php if ( $_GET['sort'] == 'id' || !isset( $_GET['sort'] ) ) echo " &#x25BC;"; ?></td>
				<th scope="col"><a href="?page=<?php echo $_GET['page']; ?>&sort=name">Name</a><?php if ( $_GET['sort'] == 'name' ) echo " &#x25BC;"; ?></td>
				<th scope="col" class="col-medium">Shortcode</th>
				<th scope="col" class="col-small">Actions</td>
			</tr>
		</thead>
		<tbody>
		<?php
			//Look for highlighted recipe
			$highlight = 0;
			if ( isset( $_GET['highlight'] ) ) {
				$highlight = $_GET['highlight'];	
			}
			
			//Display the recipes in the table
			$recipes = "";
			$sort = 'id';
			$search === null;
			
			if ( isset( $_GET['sort'] ) ) {
				$sort = $_GET['sort'];
			}
			if ( isset( $_GET['search'] ) ) {
				$search = $_GET['search'];
			}
			$recipes = GetRecipesList( $sort, $search );
	
			foreach ($recipes as $recipe) {
				if ( $highlight > 0  &&  $recipe->id == $highlight ) {
					echo '<tr class="highlight">';	
				} else {
					echo '<tr>';	
				}
					echo '	<td><input type="checkbox" class="selectbox" data-recipe-id="' . $recipe->id . '"></td>
						<td>' . $recipe->id . '</td>
						<td>' . $recipe->name . '</td>
						<td id="shortcode-' . $recipe->id . '"><input class="shortcode-input" id="shortcode-input-' . $recipe->id . '" type="text" value="[rewr id=&quot;' . $recipe->id . '&quot;]" readonly="readonly"></td>
						<td><a href="?page=' . $_GET['page'] . '&action=edit&id=' . $recipe->id . '">Edit</a></td>
					</tr>
				';
			}
	
		?>
			
		</tbody>
	</table>
	<?php elseif ( $_GET['action'] == 'edit' ) : ?>
	<?php if ( isset( $_GET['id'] ) ) {
		echo '<h1>Recipes Writer - Edit Recipe</h1>';
	} else {
		echo '<h1>Recipes Writer - New Recipe</h1>';
	} ?>
	<p id="message">No message</p>
	<?php require( __DIR__ . '/recipepanel.php' ); ?>
	<?php endif; ?>
	
	<div class="donate">
		Enjoy the plugin? It's developed by a single man operation. Support the developer by click the button below.
		<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
		<input type="hidden" name="cmd" value="_s-xclick">
		<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
		">
		<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
		<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
		</form>
	</div>
</div>