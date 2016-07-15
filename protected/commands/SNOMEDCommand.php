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
    
    public function init(){
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
        
        $this->usageError("\n\nFile not found or not readable: " . $file);
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
        $this->createAndPopulateDescriptionsTable();
        $this->createAndPopulateRelationshipsTable();
        
        return false;
    }
    
    /**
     * Creating and populating the Concepts table
     */
    private function createAndPopulateConceptsTable()
    {
        $this->db->createCommand("DROP TABLE IF EXISTS " . self::CONCEPT_TABLE)->execute();
        
        $sql = "CREATE TABLE " . self::CONCEPT_TABLE . " (
                    ConceptId BIGINT UNSIGNED NOT NULL,
                    ConceptStatus INT UNSIGNED NOT NULL,
                    FullySpecifiedName CHAR(255) NOT NULL,
                    CTV3ID CHAR(5) NOT NULL,
                    SNOMEDID CHAR(8) NOT NULL,
                    IsPrimitive BOOL,
                    PRIMARY KEY (Conceptid)
                );";
        
        $this->db->createCommand($sql)->execute();
        
        //The following command will load all the concepts. For use in OpenEyes it is recommended that only a subset is used.
        $sql = "LOAD DATA LOCAL INFILE '" . $this->conceptFile . "' INTO TABLE " . self::CONCEPT_TABLE . " IGNORE 1 LINES;";
        $this->db->createCommand($sql)->execute();
    }
    
    /**
     * Creating and populating the Descriptions table
     */
    private function createAndPopulateDescriptionsTable()
    {
        $this->db->createCommand("DROP TABLE IF EXISTS " . self::DESC_TABLE)->execute();
        
        $sql = "CREATE TABLE " . self::DESC_TABLE . " (
                    DescriptionId BIGINT UNSIGNED NOT NULL,
                    DescriptionStatus INT UNSIGNED NOT NULL,
                    ConceptId BIGINT UNSIGNED NOT NULL,
                    Term CHAR(255) NOT NULL,
                    InitialCapitalStatus BOOL,
                    DescriptionType INT UNSIGNED NOT NULL,
                    LanguageCode CHAR(8),
                    PRIMARY KEY (DescriptionId)
                );";
        
        $this->db->createCommand($sql)->execute();

        $sql = "LOAD DATA LOCAL INFILE '" . $this->descFile . "' INTO TABLE " . self::DESC_TABLE . " IGNORE 1 LINES;";
        $this->db->createCommand($sql)->execute();
    }
    
    /**
     * Creating and populating the Relation table
     */
    private function createAndPopulateRelationshipsTable()
    {
        $this->db->createCommand("DROP TABLE IF EXISTS " . self::REL_TABLE)->execute();
        
        $sql = "CREATE TABLE " . self::REL_TABLE . " (
                    RelationshipId BIGINT UNSIGNED NOT NULL, 
                    ConceptId1 BIGINT UNSIGNED NOT NULL, 
                    RelationshipType BIGINT UNSIGNED NOT NULL, 
                    ConceptId2 BIGINT UNSIGNED NOT NULL, 
                    CharacteristicType INT UNSIGNED NOT NULL, 
                    RelationshipGroup SMALLINT UNSIGNED, 
                    PRIMARY KEY (RelationshipId)
                );";
        
        $this->db->createCommand($sql)->execute();

        $sql = "LOAD DATA LOCAL INFILE '" . $this->relFile . "' INTO TABLE " . self::REL_TABLE . " IGNORE 1 LINES;";
        $this->db->createCommand($sql)->execute();

    }
    
    /** End of Creating SNOMED core tables **/
    
}