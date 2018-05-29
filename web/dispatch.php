<?php
//Check for Auth and redirect if false
session_start();
if((empty($_SESSION["authenticated"]) || $_SESSION["authenticated"] != 'true') && ($_SERVER['REQUEST_URI'] != "/login" && $_SERVER['REQUEST_URI'] != "/register")) {
    header('Location: login');
}

//Start Dispatch
$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $r->addRoute(['GET', 'POST'], '/[home]', 'home');
    $r->addRoute('GET', '/lessons[/]', 'lessons');
    $r->addRoute('GET', '/git[/]', 'git');
    $r->addRoute('GET', '/terminal[/]', 'terminal');
    $r->addRoute(['GET', 'POST'], '/settings[/]', 'settings');
    $r->addRoute(['GET','POST'], '/reboot[/]', 'reboot');
    $r->addRoute(['GET','POST'], '/csseditor[/]', 'csseditor');
    $r->addRoute('GET', '/uploads[/]', 'uploads');
    $r->addRoute(['GET','POST'], '/login[/]', 'login');
    $r->addRoute('GET', '/logout[/]', 'login');
    $r->addRoute(['GET','POST'], '/register[/]', 'register');
});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        echo $templates->render('errors/404');
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        echo $templates->render('errors/405');
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        $database = new SQLite3('armpi.sqlite');
        echo $templates->render("pages/$handler" ,['database' => $database]);
        break;
}
