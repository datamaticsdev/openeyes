<?php

class SNOMEDCommand extends CConsoleCommand
{
    
    const CONCEPT_TABLE = 'snomed_concepts';
    const DESC_TABLE    = 'snomed_descriptions';
    const REL_TABLE     = 'snomed_relationships';
    
    private $db = null;
    private $conceptFile;
    private $descFile;
    private $relFile;
    
    public function actionInit(){
        $this->db = Yii::app()->db;
    }
    
    public function getHelp()
    {
        return "\n\n!!! HELP !!!\n\n";
    }
    
    /**
     * Checking the if the file exsist and is readable
     * 
     * @param string path and file name
     * @return boolean true|false - if the file exsist and readable or not
     */
    private function validateFile($file){
        if (file_exists($file) && is_readable($file)){
            return true;
        }
        
        $this->usageError("\n\nFile not found: " . $file);
        return false;
    }
    
    /**
     * Get confirmation from the user if she/he can provide the files we will working with
     * @return bool tue|false
     */
    private function getFiles()
    {
        echo "\n\nTo proceed we will need the 3 snomed files :\n\n";
        echo "- sct1_Concepts_Core_INT_yyyymmdd.txt'\n";
        echo "- sct1_Descriptions_en_INT_yyyymmdd.txt\n";
        echo "- sct1_Relationships_Core_INT_yyyymmdd.txt\n\n";
        
        $confirm = $this->prompt("Can you provide the path of these files ? (y/n):", "y");
        
        if($confirm == "y"){
            $this->conceptFile = $this->prompt("Please type the path and file name of the Concept file:", false);
            $this->descFile = $this->prompt("Please type the path and file name of the Descriptions file:", false);
            $this->relFile = $this->prompt("Please type the path and file name of the Relationships file:", false);
        }
        
        if( $this->validateFile($this->conceptFile) && $this->validateFile($this->descFile) && $this->validateFile($this->relFile) ){
            return true;
        }
        
        return false;
    }
    
    public function actionUpdate()
    {
        // Collect the SNOMED files
        if( !$this->getFiles() ){
            echo $this->getHelp();
        }
        
        if( $this->createSnomedCoreTables() ){
            //Displays a usage error then terminate the execution
            $this->usageError("Core tables cannot be created");
        }
    }
    
    /** Creating SNOMED core tables **/
    
    private function createSnomedCoreTables()
    {
        $this->createAndPopulateConceptsTable();
        
        return false;
    }
    
    private function createAndPopulateConceptsTable(){}
    
    /** End of Creating SNOMED core tables **/
    
}