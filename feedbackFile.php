<?php

class FeedbackFile{
    public $feedbacks;
    protected $file;
    public $filename;

    public function __construct($file){
        $this->filename = $this->initFilename();
        $this->file     = $file;
        //Check if the feedbacks File is empty or not
        if (filesize($this->filename) == 0) {
            $this->feedbacks = $this->initFeedbacks();
        }else{
            $this->feedbacks = $this->getProcessedFileContent($this->filename);
        }
    }

    public function initFileName(){
        return __DIR__.'/feedbacks.txt';
    }

    public function initFeedbacks(){
        return [];
    }

    public function getFeedbacks(){
        return $this->feedbacks;
    }

    public function getProcessedFileContent($filename){
        return $this->file->getProcessedFileContent($filename);
    }

    public function putProcessedFileContent($filename,$data){
        return $this->file->putProcessedFileContent($filename,$data);
    }
}
?>