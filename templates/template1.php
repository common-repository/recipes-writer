<div {$recipe}>
	<h2 class="name">{$name}</h2>
	<div class="image">
		{$image}
	</div>
	<div class="servings">Serves {$servings}</div>
	<div class="description">{$description}</div>
	<div class="clearfix">
		<div class="print">{$print}</div><div class="share">{$share}</div>
		<div class="rating">{$rating}</div>
	</div>
	<div class="information clearfix">
		<div class="preptime"><span class="green">Preparation time</span><br>{$preptime} minutes</div>
		<div class="cooktime"><span class="green">Cooking time</span><br>{$cooktime} minutes</div>
		<div class="totaltime"><span class="green">Total time</span><br>{$totaltime} minutes</div>
	</div>
	<div class="ingredients">
		<h3>Ingredients</h3>
		{$ingredients}
	</div>
	<div class="instructions">
		<h3>Instructions</h3>
		{$instructions}
	</div>
	<div class="note">
		<h3>Notes</h3>
		<p>{$note}</p>
	</div>
</div>