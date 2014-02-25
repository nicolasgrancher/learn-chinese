<?php
// TODO Tags pour avoir thème, leçon etc.
// TODO Config : afficher pinyin et trad ?
// TODO Saisie caractère via clavier + validation ?

require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();

//$app['debug'] = true;

// definitions
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'   => 'pdo_sqlite',
        'path'     => __DIR__.'/app.db',
    ),
));
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

$app->get('/', function (Silex\Application $app) {
    $sql = "SELECT id FROM sinogram WHERE _ROWID_ >= (abs(random()) % (SELECT max(_ROWID_)+1 FROM sinogram)) LIMIT 1";
    $sinogram = $app['db']->fetchAssoc($sql);

    return $app->redirect($app['url_generator']->generate('sinogram', array('id' => $sinogram['id'])));
})->bind('randomize');

$app->get('/{id}', function (Silex\Application $app, $id) {
    $sql = "SELECT * FROM sinogram WHERE id = ?";
    $sinogram = $app['db']->fetchAssoc($sql, array((int) $id));

    if (empty($sinogram)) {
        $app->abort(404, "Sinogram $id does not exist.");
    }

    $history = $app['session']->get('history');
    if(is_null($history))
        $history = array();
    $lastElement = end($history);
    reset($history);
    if($lastElement != $id)
        $history[] = $id;
    $app['session']->set('history', $history);

    $currentIndex = count($history) - 1;

    return $app['twig']->render('sinogram.html.twig', array(
        'sinogram' => $sinogram,
        'history' => $history,
        'currentIndex' => $currentIndex,
    ));
})->bind('sinogram');

$app->get('/history/{currentIndex}', function (Silex\Application $app, $currentIndex) {
    $history = $app['session']->get('history');
    if(empty($history))
        return $app->redirect($app['url_generator']->generate('randomize'));

    $id = $history[$currentIndex];

    $sql = "SELECT * FROM sinogram WHERE id = ?";
    $sinogram = $app['db']->fetchAssoc($sql, array((int) $id));

    if (empty($sinogram)) {
        $app->abort(404, "Sinogram $id does not exist.");
    }

    return $app['twig']->render('sinogram.html.twig', array(
        'sinogram' => $sinogram,
        'history' => $history,
        'currentIndex' => $currentIndex,
    ));
})->bind('history');

$app->run();