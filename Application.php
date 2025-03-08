<?php

namespace app\core;
use app\core\db\Database;
use app\core\db\DbModel;

/**
 * Class Application
 * @package app\core
 */
class Application
{
    public static Application $app;
    public static string $ROOT_DIR;
    public string $userClass;
    public string $layout = 'main';
    public Router $router;
    public Request $request;
    public Response $response;
    public ?Controller $controller = null;
    public Database $db;
    public Session $session;
    public View $view;
    public ?UserModel $user;

    public function __construct($rootPath, array $config)
    {
        $this->user = null;
        self::$ROOT_DIR = $rootPath;
        $this->userClass = $config['userClass'];
        self::$app = $this;
        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->router = new Router($this->request, $this->response);
        $this->db = new Database($config['db']);
        $this->view = new View();

        $userId = Application::$app->session->get('user');
        if ($userId) {
            $user = new $this->userClass();
            $key = $user->primaryKey();
            $this->user = $user->findOne([$key => $userId]);
        }
    }

    public static function isGuest()
    {
        return !self::$app->user;
    }

    public function login(UserModel $user): true
    {
        $this->user = $user;
        $primaryKey = $user->primaryKey();
        $value = $user->{$primaryKey};
        Application::$app->session->set('user', $value);

        return true;
    }

    public function logout(): void
    {
        $this->user = null;
        self::$app->session->remove('user');
    }
    public function run(): void
    {
        try {
            echo $this->router->resolve();
        } catch (\Exception $e) {
            $this->response->setStatusCode($e->getCode());
            echo $this->view->renderView('_error', [
                'exception' => $e,
            ]);
        }
    }
    public function getController(): Controller
    {
        return $this->controller;
    }

    public function setController(Controller $controller): void
    {
        $this->controller = $controller;
    }

}