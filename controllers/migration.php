<?php
    class MigrationController extends ControllerBase {
        public function CreateView() {
            $handle = fopen("myfile.txt", "w");
            fwrite($handle , 'malakas');
            fclose($handle);
            include 'views/migration/create.php';     

        }
    
    
    
    }
