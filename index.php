<?php
define('MAX_TRIALS', 8);
define('PAGE_TITLE', "Le pendu");
define('REPLACEMENT_CHAR', "*");
/* define('FILE_PATH', ""); */
$words = file('datas/words.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$wordsCount = count($words);
if ($_SERVER['REQUEST_METHOD'] === "GET") {
    $wordIndex = rand(0, $wordsCount - 1);
    $word = $words[$wordIndex];
    $lettersCount = strlen($word);
    $trials = 0;
    $triedLetters = '';
    $letters = [
        'a' => true,
        'b' => true,
        'c' => true,
        'd' => true,
        'e' => true,
        'f' => true,
        'g' => true,
        'h' => true,
        'i' => true,
        'j' => true,
        'k' => true,
        'l' => true,
        'm' => true,
        'n' => true,
        'o' => true,
        'p' => true,
        'q' => true,
        'r' => true,
        's' => true,
        't' => true,
        'u' => true,
        'v' => true,
        'w' => true,
        'x' => true,
        'y' => true,
        'z' => true,
    ];
    $replacementString = str_pad("", $lettersCount, REPLACEMENT_CHAR);
} elseif ($_SERVER['REQUEST_METHOD'] === "POST") {
    $wordIndex = $_POST['wordIndex'];
    $triedLetter = $_POST['triedLetter'];
    $lettersCount = $_POST['lettersCount'];
    $replacementString = $_POST['replacementString'];
    $trials = $_POST['trials'];
    $triedLetters = $_POST['triedLetters'];
    $letters = unserialize(urldecode($_POST['serializedLetters']));
    //set up new values
    $letters[$triedLetter] = false;
    $word = strtolower($words[$wordIndex]);
    $triedLetters .= $triedLetter;
    //Checking letters int the word
    $letterFound = false;
    for ($i = 0; $i < $lettersCount; $i++) {
        if ($triedLetter === substr($word, $i, 1)) {
            $replacementString[$i] = $triedLetter;
            $letterFound = true;
        }
    }
    if (!$letterFound) {
        $trials++;
        if (MAX_TRIALS <= $trials) {
            $gameState = "lost";
        } else {
            if ($word === $replacementString) {
                $gameState = 'win';
            }
        }
    }
} else {
    header("HTTP/1.1 405 Method Not Allowed");
    exit("vous n'avez pas le droi d'exécuter cette requête");
};

echo $word ?? '';

$serializedLetters = urlencode(serialize($letters));
/* if (isset($_POST["serializedLetters"])) echo $serializedLetters; */
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <title><?= PAGE_TITLE ?></title>
</head>

<body>
    <?php if (!isset($gameState)) : ?>
        <div>
            <h1>Trouve le mot en moins de <?= MAX_TRIALS ?> essais !</h1>
        </div>
        <div>
            <p>Le mot à deviner compte <?= $lettersCount ?> lettres&nbsp;: <?= $replacementString   ?></p>
        </div>
        <div>
            <img src="images/pendu<?= $trials ?>.gif" alt="pendu niveau 0" />
        </div>
        <div>
            <?php if ($triedLetters) :
                $triedLettersAsArray = str_split($triedLetters);
                sort($triedLettersAsArray);
            ?>
                <p>Tu as essayé: <?= implode($triedLettersAsArray); ?> </p>
            <?php else : ?>
                <p>Tu n’as encore essayé aucune lettre</p>
            <?php endif ?>
        </div>
        <form action="index.php" method="post">
            <fieldset>
                <legend>Il te reste <?= MAX_TRIALS - $trials ?> essais pour sauver ta peau</legend>
                <div>
                    <label for="triedLetter">Choisis ta lettre</label>
                    <select name="triedLetter" id="triedLetter">

                        <?php foreach ($letters as $letter => $available) : ?>
                            <?php if ($available) : ?>
                                <option value="<?= $letter ?>">
                                    <?= $letter ?>
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>

                    </select>
                    <input type="hidden" name="serializedLetters" value="<?= $serializedLetters ?>" />
                    <input type="hidden" name="triedLetters" value="<?= $triedLetters ?>" />
                    <input type="hidden" name="wordIndex" value="<?= $wordIndex ?>" />
                    <input type="hidden" name="replacementString" value="<?= $replacementString ?>" />
                    <input type="hidden" name="lettersCount" value="<?= $lettersCount ?>" />
                    <input type="hidden" name="trials" value="<?= $trials ?>" />
                    <input type="submit" value="essayer cette lettre" />
                </div>

            </fieldset>
        </form>
    <?php elseif ($gameState === "win") : ?>
        <?php
        require('./views/won.php');
        ?>
    <?php else : ?>
        <?php
        require('./views/lost.php');
        ?>

    <?php endif; ?>
</body>

</html>