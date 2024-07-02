<?php

class Helpers{
    public function sanitize(string $data): string
    {
        return htmlspecialchars(stripslashes(trim($data)));
    }

    public function dd(mixed $data): void
    {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
        die();
    }

    public function flash($key, $message = null)
    {
        if ($message) {
            $_SESSION['flash'][$key] = $message;
        }
        else if (isset($_SESSION['flash'][$key])) {
            $message = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $message;
        }
    }

    public function checkSession(){
        return (isset($_SESSION['user_id']) && isset($_SESSION['username']));
    }
}

?>