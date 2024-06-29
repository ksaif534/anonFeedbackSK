<?php
ob_start();
session_start();//Start the Session
require 'helpers.php';
require 'file.php';

class Login{
    protected $helpers;
    protected $file;
    // Error Bag
    public $errors;
    //Input Data
    public $email;
    protected $password;

    public function __construct($file, $helpers, $password) {
        $this->file     = $file;
        $this->helpers  = $helpers;
        $this->password = $password;
    }

    public function initErrors(){
        return [];
    }

    public function getErrors(){
        return $this->errors;
    }

    public function getHelpers(){
        return $this->helpers;
    }

    public function login(){
        //Initialize Error Bag
        $this->errors = $this->initErrors();
        // Check if the form is submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Handle Any Errors That Occur
            // Sanitize and Validate the Email Field
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
            // Sanitize and Validate the Password Field
            if (empty($_POST['password'])) {
                $this->errors['password'] = 'Please provide a password';
                $this->helpers->flash('error',$this->errors['password']);
            } elseif (strlen($_POST['password']) < 8) {
                $this->errors['password'] = 'Password must be at least 8 characters';
                $this->helpers->flash('password',$this->errors['password']);
            } else {
                $this->password = $this->helpers->sanitize($_POST['password']);
            }
            if (empty($this->errors)) {
                //Check whether the User exists in File
                $filename = __DIR__.'/users.txt';
                $user = $this->file->getUserByEmail($filename,$this->email);
                if (!empty($user)) {
                    // Verify the user & password
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
}
$login = new Login(new File(),new Helpers(),"");
$login->login();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TruthWhisper - Anonymous Feedback App</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<header class="bg-white">
    <nav class="flex items-center justify-between p-6 lg:px-8" aria-label="Global">
        <div class="flex lg:flex-1">
            <a href="index.php" class="-m-1.5 p-1.5">
                <span class="sr-only">TruthWhisper</span>
                <span class="block font-bold text-lg bg-gradient-to-r from-blue-600 via-green-500 to-indigo-400 inline-block text-transparent bg-clip-text">TruthWhisper</span>
            </a>
        </div>
        <div class="flex lg:hidden">
            <button type="button" class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-700">
                <span class="sr-only">Open main menu</span>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>
        </div>
        <div class="hidden lg:flex lg:flex-1 lg:justify-end">
            <a href="login.php" class="text-sm font-semibold leading-6 text-gray-900">Log in <span aria-hidden="true">&rarr;</span></a>
        </div>
    </nav>
    <!-- Mobile menu, show/hide based on menu open state. -->
    <div class="lg:hidden" role="dialog" aria-modal="true">
        <!-- Background backdrop, show/hide based on slide-over state. -->
        <div class="fixed inset-0 z-10"></div>
        <div class="fixed inset-y-0 right-0 z-10 w-full overflow-y-auto bg-white px-6 py-6 sm:max-w-sm sm:ring-1 sm:ring-gray-900/10">
            <div class="flex items-center justify-between">
                <a href="index.php" class="-m-1.5 p-1.5">
                    <span class="sr-only">TruthWhisper</span>
                    <span class="block font-bold text-xl bg-gradient-to-r from-blue-600 via-green-500 to-indigo-400 inline-block text-transparent bg-clip-text">TruthWhisper</span>
                </a>
                <button type="button" class="-m-2.5 rounded-md p-2.5 text-gray-700">
                    <span class="sr-only">Close menu</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="mt-6 flow-root">
                <div class="-my-6 divide-y divide-gray-500/10">
                    <div class="py-6">
                        <a href="login.php" class="-mx-3 block rounded-lg px-3 py-2.5 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50">Log in</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<main class="">
    <div class="relative flex min-h-screen flex-col justify-center overflow-hidden bg-gray-50 py-6 sm:py-12">
        <img src="./images/beams.jpg" alt="" class="absolute top-1/2 left-1/2 max-w-none -translate-x-1/2 -translate-y-1/2" width="1308" />
        <div class="absolute inset-0 bg-[url(./images/grid.svg)] bg-center [mask-image:linear-gradient(180deg,white,rgba(255,255,255,0))]"></div>
        <div class="relative bg-white px-6 pt-10 pb-8 shadow-xl ring-1 ring-gray-900/5 sm:mx-auto sm:max-w-lg sm:rounded-lg sm:px-10">
            <div class="mx-auto max-w-xl">
                <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
                    <div class="mx-auto w-full max-w-xl text-center px-24">
                        <h1 class="block text-center font-bold text-2xl bg-gradient-to-r from-blue-600 via-green-500 to-indigo-400 inline-block text-transparent bg-clip-text">TruthWhisper</h1>
                    </div>

                    <div class="mt-10 mx-auto w-full max-w-xl">
                        <?php
                        $message = $login->getHelpers()->flash('success');
                        if ($message) : ?>
                            <div class="mt-2 bg-teal-100 border border-teal-200 text-sm text-teal-800 rounded-lg p-4" role="alert">
                                <span class="font-bold"><?= $message; ?></span>
                            </div>
                        <?php endif; ?>
                        <?php 
                        $msg = $login->getHelpers()->flash('error');
                        if ($msg) : ?>
                            <div class="mt-2 bg-red-100 border border-red-200 text-sm text-red-800 rounded-lg p-4" role="alert">
                                <span class="font-bold"><?= $msg; ?></span>
                            </div>
                        <?php endif; ?>
                        <form class="space-y-6" action="login.php" method="POST" novalidate>
                            <div>
                                <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email address</label>
                                <div class="mt-2">
                                    <input id="email" name="email" type="email" autocomplete="email" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                            </div>
                            <?php 
                            $emailErr = $login->getHelpers()->flash('email');
                            if ($emailErr) : ?>
                                <div class="mt-2 bg-red-100 border border-red-200 text-sm text-red-800 rounded-lg p-4" role="alert">
                                    <span class="font-bold"><?= $emailErr; ?></span>
                                </div>
                            <?php endif; ?>
                            <div>
                                <div class="flex items-center justify-between">
                                    <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Password</label>
                                    <div class="text-sm">
                                        <a href="#" class="font-semibold text-indigo-600 hover:text-indigo-500">Forgot password?</a>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <input id="password" name="password" type="password" autocomplete="current-password" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                            </div>
                            <?php 
                            $passwordErr = $login->getHelpers()->flash('password');
                            if ($passwordErr) : ?>
                                <div class="mt-2 bg-red-100 border border-red-200 text-sm text-red-800 rounded-lg p-4" role="alert">
                                    <span class="font-bold"><?= $passwordErr; ?></span>
                                </div>
                            <?php endif; ?>
                            <div>
                                <button type="submit" class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Sign in</button>
                            </div>
                        </form>

                        <p class="mt-10 text-center text-sm text-gray-500">
                            Not a member?
                            <a href="register.php" class="font-semibold leading-6 text-indigo-600 hover:text-indigo-500">Register now!</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<footer class="bg-white">
    <div class="mx-auto max-w-7xl px-6 py-12 md:flex md:items-center justify-center lg:px-8">
        <p class="text-center text-xs leading-5 text-gray-500">&copy; 2024 TruthWhisper, Inc. All rights reserved.</p>
    </div>
</footer>

</body>
</html>