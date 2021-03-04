<!DOCTYPE html>
<html lang="fr">

<head>
	<meta charset="UTF-8" />
	<title>Le pendu</title>
</head>

<body>
	<div>
		<h1>
			Trouve le mot en moins de
			<?= MAX_TRIALS ?>
			essais !
		</h1>
	</div>
	<div>
		<p>
			Le mot à deviner compte
			<?= $lettersCount ?>
			lettres&nbsp;:
			<?= $replacementString   ?>
		</p>
	</div>
	<div>
		<img src="images/pendu<?= $trials ?>.gif" alt="pendu niveau 0" />
	</div>
	<div>
		<?php if ($triedLetters) :
			/* $triedLettersAsArray = str_split($triedLetters);
			sort($triedLettersAsArray); 
			<?= implode($triedLettersAsArray)?>
			*/
		?>
			<p>
				Tu as essayé:
				<?= $triedLettersStr ?>
			</p>
		<?php else : ?>
			<p>Tu n’as encore essayé aucune lettre</p>
		<?php endif ?>
	</div>
	<form action="index.php" method="post">
		<fieldset>
			<legend>
				Il te reste
				<?= MAX_TRIALS - $trials ?>
				essais pour sauver ta peau
			</legend>
			<div>
				<label for="triedLetter">Choisis ta lettre</label>
				<select name="triedLetter" id="triedLetter">
					<?php foreach ($letters as $letter =>
						$available) : ?>
						<?php if ($available) : ?>
							<option value="<?= $letter ?>">
								<?= $letter ?>
							</option>
						<?php endif; ?>
					<?php endforeach; ?>
				</select>

				<input type="submit" value="essayer cette lettre" />
			</div>
		</fieldset>
	</form>
</body>

</html>