<?php

class User{
    protected File $file;

    public function __construct(File $file){
        $this->file = $file;
    }

    public function getUsers(){
        return $this->file->getData();
    }

    public function getFileName(){
        return $this->file->filename;
    }

    public function getProcessedFileContent($filename){
        return $this->file->getProcessedFileContent($filename);
    }

    public function putProcessedFileContent($filename,$data){
        return $this->file->putProcessedFileContent($filename,$data);
    }

    public function getUserByEmail($filename,$email){
        $unserializedFileContent = $this->getProcessedFileContent($filename);
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
        $max_id = 0;
        foreach ($users as $item) {
            if ($item['id'] > $max_id) {
                $max_id = $item['id'];
            }
        }
        $new_id = $max_id + 1;
        $updatedUser = [
            'id'        => $new_id,
            'name'      => $user['name'],
            'email'     => $user['email'],
            'password'  => $user['password']
        ];
        return $updatedUser;
    }
}

?>