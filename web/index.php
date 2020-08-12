<?php

require('../vendor/autoload.php');

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
// Set the path to where the html and twig files are

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

// Our web handlers
// Render index.twig file when the URL is /

$app->get('/', function() use($app) {
  $app['monolog']->addDebug('logging output.');
  return $app['twig']->render('index.twig');
});

// When href gets /display/
// Run the query that selects all the students in the database
// Render database.twig file using the data

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

// When href gets /male/
// Run the query that selects all the male students in the database
// Render database.twig file using the data

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

// When href gets /female/
// Run the query that selects all the female students in the database
// Render database.twig file using the data

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

// When href gets /insert/
// Get the data about the student to insert
// Insert the data into the database
// Run the query that selects all the students in the database
// Render database.twig file using the data

$app->get('/insert/', function() use($app) {

  $name = $_GET["name"];
  $age = $_GET["age"];
  $gender = $_GET["gender"];
  $phone = $_GET["phone"];

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

// When href gets /delete/
// Get the data about the student to delete
// Insert the data into the database
// Run the query that selects all the students in the database
// Render database.twig file using the data

$app->get('/delete/', function() use($app) {
  // $name = $GLOBALS['stdName'];
  $name = $_GET["name"];
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
