<?php
    abstract class Migration {
        protected static function migrate( $sql ) {
            include_once '../../config/config-local.php';
            include_once '../../models/database.php';
            include_once '../../models/db.php';

            global $config;

            $config = getConfig()[ getEnv( 'ENVIRONMENT' ) ]; 
            dbInit();

            try {
                $res = db( $sql );
            }
            catch ( DBException $e ) {
                die( "Migration failed. SQL query died with the following error: " . mysql_error() . "\n" );
            }
            echo "Migration successful.\n";
        } 
    
        public static function addField( $table, $field, $description ) {
            $sql = "ALTER TABLE
                        $table
                    ADD COLUMN
                        `field` $description;";
            self::migrate( $sql );              
        }
    
        public static function renameField( $table, $oldName, $newName, $description ) {
            $sql = "ALTER TABLE
                        $table
                    CHANGE
                        `$oldName` `$newName` $description;";
            self::migrate( $sql );
        }
    
        public static function DropField ($table, $field ) {
            $sql = "ALTER TABLE
                        $table
                    DROP COLUMN
                        $field;";
            self::migrate( $sql );
        }
	
	    public static function createTable( $tableName, $fields = [], $keys = [] ) {
		    foreach ( $fields as $field => $description ) {
			    $attributes[] = "$field $description";
		    }
            if ( !empty( $keys ) ) {
                foreach ( $keys as $key => $value ) {
                    if ( $key == 'unique' ) {
                        foreach ( $value as $string ) {
                            $args[] = "UNIQUE KEY $string";
                        }
                    } 
                    else {
                        foreach ( $value as $string ) {
                            $args[] = "PRIMARY KEY $string";
                        }
                    }
                }
                $attributes = array_merge( $attributes, $args );
            } 
            $attributes = implode( ',', $attributes );
            $sql = "CREATE TABLE IF NOT EXISTS
                $tableName (
                    $attributes
                )
                ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
		    self::migrate( $sql );
	    }
    }
?>
