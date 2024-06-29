<?php
session_start();//Start Session
require 'helpers.php';
require 'file.php';

class Register{
    protected $helpers;
    protected $file;
    // Error Bag
    protected $errors;
    //Input Data
    public $name;
    public $email;
    protected $password;

    public function __construct($file,$helpers,$errors,$password){
        $this->file             = $file;
        $this->helpers          = $helpers;
        $this->errors           = $errors;
        $this->password         = $password;
    }

    public function getErrors(){
        return $this->errors;
    }

    public function getHelpers(){
        return $this->helpers;
    }

    public function register(){
        // Input Data
        $this->name = '';
        $this->email = '';
        // Check if the form is submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Handle Any Errors That Occur
            // Sanitize and Validate the Name Field
            if (empty($_POST['name'])) {
                $this->errors['name'] = 'Please provide a name';
                $this->helpers->flash('name',$this->errors['name']);
            } else {
                $this->name = $this->helpers->sanitize($_POST['name']);
            }
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
                $this->helpers->flash('password',$this->errors['password']);
            } elseif (strlen($_POST['password']) < 8) {
                $this->errors['password'] = 'Password must be at least 8 characters';
                $this->helpers->flash('password',$this->errors['password']);
            } else {
                //Check if the raw password mathces the confirm password
                if (($_POST['password']) !== $_POST['confirm_password']) {
                    $this->errors['confirm_password'] = 'Password and Confirm Password do not match';
                    $this->helpers->flash('confirm_password',$this->errors['confirm_password']);
                }
                $this->password = $this->helpers->sanitize($_POST['password']);
                // Hash The Password
                $this->password = password_hash($this->password, PASSWORD_DEFAULT);
            }
            if (empty($this->errors)) {
                //Store Registration Data in File
                //Prepare the User Record
                $user = [
                    'name'              => $this->name,
                    'email'             => $this->email,
                    'password'          => $this->password
                ];
                //Update with Auto Increment
                $user = $this->file->updatedFileInputWithAutoIncrement($this->file->getUsers(),$user);
                //Store the User in Users File
                $users = $this->file->getUsers();
                array_push($users,$user);
                $filename = $this->file->initFileName();
                if ($this->file->putProcessedFileContent($filename,$users)) {
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
}
$register = new Register(new File(),new Helpers(),[],"");
$register->register();
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
                        $msg = $register->getHelpers()->flash('error');
                        if ($msg) : ?>
                            <div class="mt-2 bg-teal-100 border border-teal-200 text-sm text-teal-800 rounded-lg p-4" role="alert">
                                <span class="font-bold"><?= $msg; ?></span>
                            </div>
                        <?php endif; ?>
                        <form class="space-y-6" action="register.php" method="POST" novalidate>
                            <div>
                                <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Name</label>
                                <div class="mt-2">
                                    <input id="name" name="name" type="text" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                            </div>
                            <?php
                            $nameErr = $register->getHelpers()->flash('name');
                            if ($nameErr) : ?>
                                <div class="mt-2 bg-teal-100 border border-teal-200 text-sm text-teal-800 rounded-lg p-4" role="alert">
                                    <span class="font-bold"><?= $nameErr; ?></span>
                                </div>
                            <?php endif; ?>
                            <div>
                                <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email address</label>
                                <div class="mt-2">
                                    <input id="email" name="email" type="email" autocomplete="email" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                            </div>
                            <?php
                            $emailErr = $register->getHelpers()->flash('email');
                            if ($emailErr) : ?>
                                <div class="mt-2 bg-teal-100 border border-teal-200 text-sm text-teal-800 rounded-lg p-4" role="alert">
                                    <span class="font-bold"><?= $emailErr; ?></span>
                                </div>
                            <?php endif; ?>
                            <div>
                                <div class="flex items-center justify-between">
                                    <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Password</label>
                                </div>
                                <div class="mt-2">
                                    <input id="password" name="password" type="password" autocomplete="current-password" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                            </div>
                            <?php
                            $passErr = $register->getHelpers()->flash('password');
                            if ($passErr) : ?>
                                <div class="mt-2 bg-teal-100 border border-teal-200 text-sm text-teal-800 rounded-lg p-4" role="alert">
                                    <span class="font-bold"><?= $passErr; ?></span>
                                </div>
                            <?php endif; ?>
                            <div>
                                <div class="flex items-center justify-between">
                                    <label for="confirm_password" class="block text-sm font-medium leading-6 text-gray-900">Confirm Password</label>
                                </div>
                                <div class="mt-2">
                                    <input id="confirm_password" name="confirm_password" type="password" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                            </div>
                            <?php
                            $confirmPass = $register->getHelpers()->flash('confirm_password');
                            if ($confirmPass) : ?>
                                <div class="mt-2 bg-teal-100 border border-teal-200 text-sm text-teal-800 rounded-lg p-4" role="alert">
                                    <span class="font-bold"><?= $confirmPass; ?></span>
                                </div>
                            <?php endif; ?>
                            <div>
                                <button type="submit" class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Register</button>
                            </div>
                        </form>

                        <p class="mt-10 text-center text-sm text-gray-500">
                            Already have an account?
                            <a href="./login.php" class="font-semibold leading-6 text-indigo-600 hover:text-indigo-500">Login!</a>
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