<?php
// TODO Tags pour avoir thème, leçon etc.
// TODO Caractères vus en session -> retour arrière possible pour revoir les caractères
// TODO Timeline en haut pour voir sa position par rapport à la session
// TODO NB de caractères vus
// TODO Config : afficher pinyin et trad ?
// TODO Saisie caractère via clavier + validation ?

require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();

$app['debug'] = true;

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

$app->get('/{id}', function (Silex\Application $app, $id) {
    $sql = "SELECT * FROM sinogram WHERE id = ?";
    $sinogram = $app['db']->fetchAssoc($sql, array((int) $id));

    if (empty($sinogram)) {
        $app->abort(404, "Sinogram $id does not exist.");
    }

    return $app['twig']->render('sinogram.html.twig', array(
        'sinogram' => $sinogram,
    ));
});
/*$app->post('/feedback', function (Request $request) {
    $message = $request->get('message');
    mail('feedback@yoursite.com', '[YourSite] Feedback', $message);

    return new Response('Thank you for your feedback!', 201);
});*/

$app->run();