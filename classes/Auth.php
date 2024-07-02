<?php

class Auth{
    public $name;
    public $email;
    protected $password;
    protected $file;
    protected $errors;
    protected $helpers;

    public function __construct($password, $helpers,$errors,$file){
        $this->password = $password;
        $this->helpers  = $helpers;
        $this->errors   = $errors;
        $this->file     = $file;
    }

    public function register(){
        $this->name = '';
        $this->email = '';
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->sanitizeName();
            $this->sanitizeEmail();
            $this->sanitizePassword();
            if (empty($this->errors)) {
                $user = [
                    'name'              => $this->name,
                    'email'             => $this->email,
                    'password'          => $this->password
                ];
                $user = $this->file->updatedFileInputWithAutoIncrement($this->file->getUsers(),$user);
                $users = $this->file->getUsers();
                array_push($users,$user);
                if ($this->file->putProcessedFileContent($this->file->getFileName(),$users)) {
                    $this->helpers->flash('success', 'You have successfully registered. Please log in to continue');
                    header('Location: login.php');
                    exit;
                } else {
                    $this->errors['auth_error'] = 'An error occurred. Please try again';
                    $this->helpers->flash('error',$this->errors['auth_error']);
                }
            }
        }
    }

    public function login(){
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->sanitizeEmail();
            $this->sanitizePassword();
            if (empty($this->errors)) {
                $user = $this->file->getUserByEmail($this->file->getFileName(),$this->email);
                if (!empty($user)) {
                    var_dump(password_verify($this->password, $user['password']));
                    if ($user && password_verify($this->password, $user['password'])) {
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['name'];
                        header('Location: dashboard.php');
                        exit;
                    } else {
                        $this->errors['auth_error'] = 'Invalid email or password';
                        $this->helpers->flash('error',$this->errors['auth_error']);
                    }
                } else {
                    $this->errors['auth_error'] = 'An error occurred. Please try again';
                    $this->helpers->flash('error',$this->errors['auth_error']);
                }
            }
        }
    }

    public function sanitizeName(){
        if (empty($_POST['name'])) {
            $this->errors['name'] = 'Please provide a name';
            $this->helpers->flash('name',$this->errors['name']);
        } else {
            $this->name = $this->helpers->sanitize($_POST['name']);
        }
    }

    public function sanitizeEmail(){
        if (empty($_POST['email'])) {
            $this->errors['email'] = 'Please provide an email address';
            $this->helpers->flash('email',$this->errors['email']);
        } else {
            $this->email = $this->helpers->sanitize($_POST['email']);
            if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
                $this->errors['email'] = 'Please provide a valid email address';
                $this->helpers->flash('email',$this->errors['email']);
            }
        }
    }

    public function sanitizePassword(){
        if (empty($_POST['password'])) {
            $this->errors['password'] = 'Please provide a password';
            $this->helpers->flash('password',$this->errors['password']);
        } elseif (strlen($_POST['password']) < 8) {
            $this->errors['password'] = 'Password must be at least 8 characters';
            $this->helpers->flash('password',$this->errors['password']);
        } else {
            if (isset($_POST['confirm_password'])) {
                $this->checkPasswordConfirmation();
                $this->password = $this->helpers->sanitize($_POST['password']);
                $this->password = password_hash($this->password, PASSWORD_DEFAULT);
            }else{
                $this->password = $this->helpers->sanitize($_POST['password']);
            }
        }
    }

    public function checkPasswordConfirmation(){
        if (($_POST['password']) !== $_POST['confirm_password']) {
            $this->errors['confirm_password'] = 'Password and Confirm Password do not match';
            $this->helpers->flash('confirm_password',$this->errors['confirm_password']);
        }
    }

    public function getHelpers(){
        return $this->helpers;
    }
}

?>