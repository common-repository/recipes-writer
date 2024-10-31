<div {$recipe}>
	<div class="header">
		<h2 class="name">{$name}</h2>
	</div>
	<div class="image">
		{$image}
	</div>
	<div class="smallcolumn smallinfo">
		<div class="preptime">Preparation time<br>{$preptime}</div>
	</div>
	<div class="smallcolumn smallinfo smallinfo-middle">
		<div class="cooktime">Cooking time<br>{$cooktime}</div>
	</div>
	<div class="smallcolumn smallinfo">
		<div class="servings">Servings<br>{$servings}</div>
	</div>
	<div class="description">
		<h3>Description</h3>
		<p>{$description}</p>
	</div>
	<div class="clearfix">
		<div class="information">
			<h3>Information</h3>
			<div class="author">By: {$author}</div>
			<div class="category">Category: {$category}</div>
			<div class="cuisine">Cuisine: {$cuisine}</div>
			<div class="rating">Rating: {$rating}</div>
		</div>
		<div class="ingredients">
			<h3>Ingredients</h3>
			{$ingredients}
		</div>
	</div>
	<div class="instructions">
		<h3>Instructions</h3>
		{$instructions}
	</div>
	<div class="note">
		<h3>Note</h3>
		<p>{$note}</p>
	</div>
	<div class="url">
		Originally from {$blogname} at {$url}	
	</div>
</div>