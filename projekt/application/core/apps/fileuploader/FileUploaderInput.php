<?php
class FileUploaderInput {

    private $fileUploader;
    private $limits = array();

    function __construct($maxFiles = 20, $maxFileSize = "3Mb", $allowedFormats = ""){

        $this->fileUploader = new FileUploader();
        $this->fileUploader->setLimits($maxFileSize, $maxFiles, $allowedFormats);

        $this->updateLimits(array("maxFiles" => $this->fileUploader->getMaxNumOfFiles(), "maxPost" => $this->fileUploader->getMaxPostSize(), "maxUpload" => $this->fileUploader->getMaxUploadSize(), "disabled" => $this->fileUploader->getDisabledExtensions(), "allowedExt" => $this->fileUploader->getAllowedExtensions()));

    }

    private function updateLimits($params = array()){

        if(!empty($params)){

            foreach($params as $key => $value){
                $this->limits[$key] = $value;
            }

        }

    }

    public function changeSettings($maxFiles = 20, $maxFileSize = "3Mb", $allowedFormats = ""){

        $this->fileUploader->setLimits($maxFileSize, $maxFiles, $allowedFormats);
        $this->updateLimits(array("maxFiles" => $this->fileUploader->getMaxNumOfFiles(), "maxPost" => $this->fileUploader->getMaxPostSize(), "maxUpload" => $this->fileUploader->getMaxUploadSize(), "allowedExt" => $this->fileUploader->getAllowedExtensions()));

    }

    private function joinLimits(){

        $limits = "";

        foreach($this->limits as $value){
            if($value != '') {
                if ($limits == "") {
                    $limits .= $value;
                } else {
                    $limits .= "," . $value;
                }
            }
        }

        return $limits;

    }

    public function defaultParameters(){

        $this->fileUploader->restoreLimits();
        $this->updateLimits(array("maxFiles" => $this->fileUploader->getMaxNumOfFiles(), "maxPost" => $this->fileUploader->getMaxPostSize(), "maxUpload" => $this->fileUploader->getMaxUploadSize(), "disabled" => $this->fileUploader->getDisabledExtensions(), "allowedExt" => $this->fileUploader->getAllowedExtensions()));

    }

    public function Extended($name = "", $required = false)
    {
        if ($name != '') {

            $limits = $this->joinLimits();

            return '
                <div class="FileUploaderExtendetContainer">
                <input name="'.$name.'limit" type="text" value="' . $limits . '" style="display: none;"/>
                <input name="'.$name.'" class="FileUploaderExtendet" type="file" '.($required ? 'required' : '').' multiple>
                </div>

                 <script type="text/javascript">
                    if(window.File && window.FileList && window.FileReader) {
                        FileUploaderExtendet.update();
                    }
                </script>

            ';
        } else {
            return "Není uvedeno jméno input file";
        }
    }

    public function Classic($name = "",$required = false){

        if ($name != '') {

            $limits = $this->joinLimits();

            return '
                 <div class="FileUploaderBasicContainer">
                 <input name="'.$name.'limit" type="text" value="'.$limits.'" style="display: none;"/>
                 <input name="'.$name.'" type="file" class="FileUploaderBasic" '.($required ? 'required' : '').' multiple/>
                 </div><br style="clear: both;"/>

                <script type="text/javascript">
                    if(window.File && window.FileList && window.FileReader) {
                        FileUploaderBasic.update();
                    }
                </script>
            ';

        }
    }
}

?>
