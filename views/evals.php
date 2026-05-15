<h2>Evaluaciones</h2>
<?php foreach($e as $x){ ?>
<div><?=$x['title']?>
<form method=POST action=answer>
<input type=hidden name=id value=<?=$x['id']?>>
<button>Responder</button>
</form></div>
<?php } ?>
