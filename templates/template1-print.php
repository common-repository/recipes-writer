<div {$recipe}>
	<div class="image">
		{$image}
	</div>
	<h2 class="name">{$name}</h2>
	<span class="green">Preparation time : </span>{$preptime} minutes<br>
	<span class="green">Cooking time : </span>{$cooktime} minutes<br>
	<span class="green">Total time : </span>{$totaltime} minutes<br>
	<span class="green">Servings : </span> {$servings}<br>
	<div class="description">{$description}</div>
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