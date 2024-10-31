<div {$recipe}>
	<div class="clearfix">
		<div class="share">{$share}</div>
		<div class="print">{$print}</div>	
	</div>
	<h2 class="name">{$name}</h2>
	<div class="image">
		{$image}
	</div>
	<div class="description">
		<h3>Description</h3>
		<p>{$description}</p>
	</div>
	<div class="clearfix">
		<div class="information">
			<h3>Information</h3>
			<div class="author">By: {$author}</div>
			<div class="servings">Servings: {$servings}</div>
			<div class="preptime">Preparation time: {$preptime}</div>
			<div class="cooktime">Cooking time: {$cooktime}</div>
			<div class="totaltime">Total time: {$totaltime}</div>
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
</div>