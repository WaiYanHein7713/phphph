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
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

// Our web handlers

$app->get('/', function() use($app) {
  $app['monolog']->addDebug('logging output.');
  return $app['twig']->render('index.twig');
});

$app->get('/view/', function() use($app) {
  $st = $app['pdo']->prepare("SELECT * FROM students WHERE \"StdGender\"='Male'");
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

$app->get('/add/', function() use($app) {
  $st = $app['pdo']->prepare("INSERT INTO students (\"StdName\",\"StdAge\",\"StdGender\",\"StdPhone\") VALUES ('Kaung Sett Thu', 19, 'Male', '09 950238593')");
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

// $app->get('/insert',function() use $app {
//
//     $app['pdo']->insert('students', array(
//         'StdName' => 'Kaung Sett Thu',
//         'StdAge' => 19,
//         'StdGender' => 'Male',
//         'StdPhone' => '09 950238593'
//       )
//     );
//
// });

$app->run();

// fuction insertData ($stdName, $stdAge, $stdGender, $stdPhone){
//   $app->get('/db/', function() use($app) {
//     $st = $app['pdo']->prepare('INSERT INTO students (StdName, StdAge, StdGender, StdPhone) VALUES ('$stdName',$stdAge,'$stdGender','$stdPhone')');
//     $st->execute();
//
//     $StdName = array();
//     while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
//       $app['monolog']->addDebug('Row ' . $row['StdName']);
//       $StdName[] = $row;
//     }
//
//     return $app['twig']->render('database.twig', array(
//       'StdName' => $StdName
//     ));
//   });
//
//   $app->run();
// }
