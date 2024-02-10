<?php

namespace Asko\Sember;

use Ramsey\Uuid\Uuid;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

class View
{
    /**
     * Render a view with Twig.
     *
     * @param string $view
     * @param array $data
     * @return string
     */
    public static function render(string $view, array $data = []): string
    {
        $twig_loader = new FilesystemLoader(NTH_ROOT . '/views');
        $twig = new Environment($twig_loader, [
            'cache' => NTH_ROOT . '/storage/views',
            'debug' => true,
        ]);

        // Create csrf token and return it
        $twig->addFunction(new TwigFunction('csrf_token', function () {
            $token = Uuid::uuid4()->toString();
            $_SESSION['csrf_token'] = $token;

            return $token;
        }));

        // Create csrf token, input, and return it
        $twig->addFunction(new TwigFunction('csrf_field', function () {
            $token = Uuid::uuid4()->toString();
            $_SESSION['csrf_token'] = $token;

            return "<input type=\"hidden\" name=\"csrf_token\" value=\"{$token}\">";
        }));

        try {
            return $twig->render("{$view}.twig", $data);
        } catch (LoaderError | RuntimeError | SyntaxError $e) {
            return $e->getMessage();
        }
    }
}