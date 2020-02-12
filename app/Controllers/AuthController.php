<?php
namespace App\Controllers;

use App\Models\User;
use Respect\Validation\Validator as v;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Diactoros\ServerRequest;

class AuthController extends BaseController {
    public function getLogin() {
        return $this->renderHTML('login.twig');
    }

    public function postLogin( ServerRequest $request) {
        $postData = $request->getParsedBody();
        $responseMessage = null;
        // validaciond de usuario

        $user = User::where('email', $postData['email'])->first();
        if($user) {
            if (\password_verify($postData['password'], $user->password)) {
                //super global de seccion
                $_SESSION['userId'] = $user->id;
                return new RedirectResponse('/php/admin');
            } else {
               $responseMessage = 'Bad credentials';
            }
        } else {
            $responseMessage = 'Bad credentials';
        }

        return $this->renderHTML('login.twig', [
            'responseMessage' => $responseMessage
        ]);
    }

    public function getLogout() {
       //unset nos permite eliminar un elemento de un arreglo
        unset($_SESSION['userId']);
        return new RedirectResponse('/php/login');
    }
}