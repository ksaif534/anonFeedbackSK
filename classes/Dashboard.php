<?php

class Dashboard{
    protected File $file;

    public function __construct(File $file) {
        $this->file = $file;
    }

    public function getFeedbacks(){
        return $this->file->getData();
    }

    public function checkAuth(){
        if (!isset($_SESSION['user_id']) && !isset($_SESSION['username'])) {
            header("Location: login.php");
            exit;
        }
    }

    public function generateUniqueLink(){
        $uniqueLink = bin2hex(random_bytes(16));
        return $uniqueLink;
    }

    public function getCurrentDirUrl(){
        $protocol       = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';
        $host           = $_SERVER['HTTP_HOST'];
        $requestURI     = $_SERVER['REQUEST_URI'];
        $dirURI         = dirname($requestURI);
        $directoryUrl   = $protocol . '://' . $host . $dirURI . '/';
        return $directoryUrl;
    }
}

?>