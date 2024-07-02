<?php

class Feedback{
    public $feedback;
    protected File $file;
    protected $errors;
    protected $helpers;

    public function __construct(File $file,$errors,$helpers){
        $this->file     = $file;
        $this->errors   = $errors;
        $this->helpers  = $helpers;
    }

    public function storeFeedback(){
        $this->feedback = '';
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $link = isset($_POST['link']) ? $_POST['link'] : '';
            $this->sanitizeFeedback();
            if (empty($this->errors)) {
                $feedback = [
                    'link'      => $link,
                    'feedback'  => $this->feedback
                ];
                $feedbacks = $this->getFeedbacks();
                array_push($feedbacks,$feedback);
                if ($this->putProcessedFileContent($this->getFileName(),$feedbacks)) {
                    $this->helpers->flash('success', 'You have successfully stored the feedback');
                    header('Location: feedback-success.php');
                    exit;
                }else{
                    $this->errors['feedback_error'] = 'A feedback error occured. Please try again';
                }
            }
        }
    }

    public function getFileName(){
        return $this->file->filename;
    }

    public function putProcessedFileContent($filename,$data){
        return $this->file->putProcessedFileContent($filename,$data);
    }

    public function getFeedbacks(){
        return $this->file->getData();
    }

    public function sanitizeFeedback(){
        if(empty($_POST['feedback'])){
            $this->errors['feedback'] = 'Please provide the feedback';
        }else{
            $this->feedback = $this->helpers->sanitize($_POST['feedback']);
        }
    }
}

?>