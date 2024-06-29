<?php

class File{
    protected $users;
    public $filename;

    public function __construct(){
        $this->filename = $this->initFileName();
        //Check if the Users File is empty or not
        if (filesize($this->filename) == 0) {
            $this->users = $this->initUsers();
        }else{
            $this->users = $this->getProcessedFileContent($this->filename);
        }
    }

    public function initFileName(){
        return __DIR__.'/users.txt';
    }

    public function initUsers(){
        return [];
    }

    public function getUsers(){
        return $this->users;
    }

    public function getProcessedFileContent($filename){
        //Get the Serialized File Content
        $serializedFileContent = file_get_contents($filename);
        //Convert into array
        $unserializedFileContent = unserialize($serializedFileContent);
        return $unserializedFileContent;
    }

    public function putProcessedFileContent($filename,$data){
        //Serialize the Array Data
        $serializedFileContent = serialize($data);
        //Put it into the file
        return file_put_contents($filename,$serializedFileContent);
    }

    public function getUserByEmail($filename,$email){
        //Unserialize and convert to array
        $unserializedFileContent = $this->getProcessedFileContent($filename);
        //Do the Query
        $query = [];
        foreach ($unserializedFileContent as $user) {
            if ($user['email'] == $email) {
                $query = $user;
                break;
            }
        }
        return $query;
    }

    public function updatedFileInputWithAutoIncrement($users,$user){
        // Find the highest ID in the array
        $max_id = 0;
        foreach ($users as $item) {
            if ($item['id'] > $max_id) {
                $max_id = $item['id'];
            }
        }
        // Increment the ID for the new item
        $new_id = $max_id + 1;
        // New item data
        $updatedUser = [
            'id'        => $new_id,
            'name'      => $user['name'],
            'email'     => $user['email'],
            'password'  => $user['password']
        ];
        //Return Updated User
        return $updatedUser;
    }
}