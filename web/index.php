<?php

require('../vendor/autoload.php');

// $nametemp = $_POST["name"];
if ($_POST["name"] =! null){
  header("Location: /insert/");
}
// $agetemp = $_POST["age"];
// $gendertemp = $_POST["gender"];
// $phonetemp = $_POST["phone"];
//
// $GLOBALS['stdName'] = $nametemp;
// $GLOBALS['stdAge'] = $agetemp;
// $GLOBALS['stdGender'] = $gendertemp;
// $GLOBALS['stdPhone'] = $phonetemp;

// $GLOBALS['stdName'] = "Kaung Sett Thu";
// $GLOBALS['stdAge'] = 19;
// $GLOBALS['stdGender'] = "Male";
// $GLOBALS['stdPhone'] = "09 950249109";

// echo $GLOBALS['stdName'];
// echo $GLOBALS['stdAge'];
// echo $GLOBALS['stdGender'];
// echo $GLOBALS['stdPhone'];

$app = new Silex\Application();
$app['debug'] = true;

$dbopts = parse_url(getenv('DATABASE_URL'));
$app->register(new Csanquer\Silex\PdoServiceProvider\Provider\PDOServiceProvider('pdo'),
  array(
   'pdo.server' => array(
   'driver'   => 'pgsql',
   'user' => $dbopts["user"],
   'password' => $dbopts["pass"],
   'host' => $dbopts["host"],
   'port' => $dbopts["port"],
   'dbname' => ltrim($dbopts["path"],'/')
  )
 )
);

// Register the monolog logging service
$app->register(new Silex\Provider\MonologServiceProvider(), array(
  'monolog.logfile' => 'php://stderr',
));

// Register view rendering
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

// Our web handlers

$app->get('/', function() use($app) {
  $app['monolog']->addDebug('logging output.');
  return $app['twig']->render('index.twig');
});

$app->get('/display/', function() use($app) {
  $st = $app['pdo']->prepare("SELECT * FROM students ORDER BY \"StdName\" ASC");
  $st->execute();

  $StdName = array();
  while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
    $app['monolog']->addDebug('Row ' . $row['StdName']);
    $StdName[] = $row;
  }

  return $app['twig']->render('database.twig', array(
    'StdName' => $StdName
  ));
});

$app->get('/male/', function() use($app) {
  $st = $app['pdo']->prepare("SELECT * FROM students WHERE \"StdGender\" = 'Male' ORDER BY \"StdName\" ASC");
  $st->execute();

  $StdName = array();
  while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
    $app['monolog']->addDebug('Row ' . $row['StdName']);
    $StdName[] = $row;
  }

  return $app['twig']->render('database.twig', array(
    'StdName' => $StdName
  ));
});

$app->get('/female/', function() use($app) {
  $st = $app['pdo']->prepare("SELECT * FROM students WHERE \"StdGender\" = 'Female' ORDER BY \"StdName\" ASC");
  $st->execute();

  $StdName = array();
  while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
    $app['monolog']->addDebug('Row ' . $row['StdName']);
    $StdName[] = $row;
  }

  return $app['twig']->render('database.twig', array(
    'StdName' => $StdName
  ));
});

$app->get('/insert/', function() use($app) {
  // $name = $GLOBALS['stdName'];
  // $age = $GLOBALS['stdAge'];
  // $gender = $GLOBALS['stdGender'];
  // $phone = $GLOBALS['stdPhone'];
  $name = $_POST["name"];
  $age = $_POST["age"];
  $gender = $_POST["gender"];
  $phone = $_POST["phone"];
  echo $name;
  echo $age;
  echo $gender;
  echo $phone;
  $st = $app['pdo']->prepare("INSERT INTO students (\"StdName\",\"StdAge\",\"StdGender\",\"StdPhone\") VALUES ('$name', $age, '$gender', '$phone')");
  $st->execute();

  $st = $app['pdo']->prepare("SELECT * FROM students ORDER BY \"StdName\" ASC");
  $st->execute();

  $StdName = array();
  while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
    $app['monolog']->addDebug('Row ' . $row['StdName']);
    $StdName[] = $row;
  }

  return $app['twig']->render('database.twig', array(
    'StdName' => $StdName
  ));
});

$app->get('/delete/', function() use($app) {
  // $name = $GLOBALS['stdName'];
  $name = $_POST["name"];
  $st = $app['pdo']->prepare("DELETE FROM students WHERE \"StdName\" = '$name'");
  $st->execute();

  $st = $app['pdo']->prepare("SELECT * FROM students ORDER BY \"StdName\" ASC");
  $st->execute();

  $StdName = array();
  while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
    $app['monolog']->addDebug('Row ' . $row['StdName']);
    $StdName[] = $row;
  }

  return $app['twig']->render('database.twig', array(
    'StdName' => $StdName
  ));
});

$app->run();

?>
