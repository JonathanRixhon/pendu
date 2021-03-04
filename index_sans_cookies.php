<?php
$start = microtime(true);

require "./configs/config.php";


$words = file('datas/words.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$wordsCount = count($words);

if ($_SERVER['REQUEST_METHOD'] === "GET") {

    $wordIndex = rand(0, $wordsCount - 1);
    $word = $words[$wordIndex];
    $lettersCount = strlen($word);
    $trials = 0;
    $triedLetters = [];
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
    //Données de la requete
    $wordIndex = $_POST['wordIndex'];
    $word = strtolower($words[$wordIndex]);
    $triedLetter = $_POST['triedLetter'];
    $letters = unserialize(urldecode($_POST['serializedLetters']));
    //calcul des valeurs du state
    $letters[$triedLetter] = false;
    $triedLetters = array_filter($letters, fn ($b) => !$b);

    /* 
    en fleché on peut utilisé $word;
    $trials = count(array_filter(array_keys($triedLetters), function ($l) use ($word) {
        return !str_contains($word, $l);
    })); */

    $trials = count(array_filter(array_keys($triedLetters), fn ($l) => !str_contains($word, $l)));
    $lettersCount = strlen($word);
    $replacementString = str_pad("", $lettersCount, REPLACEMENT_CHAR);

    /* $replacementString = str_pad("", $lettersCount, REPLACEMENT_CHAR); */
    //Checking letters int the word
    $letterFound = false;
    for ($i = 0; $i < $lettersCount; $i++) {
        $replacementString[$i] = array_key_exists($word[$i], $triedLetters) ? $word[$i] : REPLACEMENT_CHAR;
        if ($triedLetter === substr($word, $i, 1)) {
            $letterFound = true;
        }
    }

    //
    if (!$letterFound) {
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
$triedLettersStr = implode(',', array_keys($triedLetters))
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

        <?php require('./views/start.php'); ?>
    <?php elseif ($gameState === "win") : ?>
        <?php require('./views/won.php'); ?>
    <?php else : ?>
        <?php require('./views/lost.php'); ?>
    <?php endif; ?>
</body>

</html>

<?php
$end = microtime(true);
$rendertime = ($end - $start) * 1000;
printf("Rendu en %.6f ms.", $rendertime)
?>