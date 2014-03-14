<?php
    abstract class Migration {
        protected static function migrate( $sql ) {
            global $config;

            require_once 'helpers/config.php';
            require_once 'models/database.php';
            require_once 'models/db.php';

            if ( isset( $GLOBALS[ 'env' ] ) ) {
                $env = $GLOBALS[ 'env' ];
            }
            else {
                $env = getEnv( 'ENVIRONMENT' );
            }
            $config = getConfig()[ $env ]; 
            dbInit();

            try {
                $res = db( $sql );
            }
            catch ( DBException $e ) {
                throw new MigrationException( $e );
            }
        } 
        public static function createLog( $name, $env ) {
            $path = 'database/migration/' . $env . '.txt';
            $fh = fopen( $path, 'w' ) or die( "can't open file" );
            fwrite( $fh, $name );
            fclose( $fh );
        }

        public static function getUnexecuted( $env ) {
            $last = self::getLast( $env );
            $migrations = self::findAll();
            $delete = true;
            foreach( $migrations as $key => $migration ) {
                if( $migration == $last || $delete ) {
                    unset( $migrations[ $key ] );
                    $delete = false;
                }
            }
            return $migrations;
        }

        public static function getLast( $env = 'development' ) {
            ob_start();
            include 'database/migration/' . $env . '.txt';
            return ob_get_clean();
        }
            
        public static function findAll() {
            $array = [];
            $handle = opendir( 'database/migration/' );
            while ( false !== ( $entry = readdir( $handle ) ) ) {
                if ( $entry != "." && $entry != ".." && $entry != "log.txt" ) {
                    $array[] = $entry;
                }
            }
            array_multisort( $array, SORT_ASC, $array );
            return $array;
        }

        public static function addField( $table, $field, $description ) {
            self::migrate( 
                "ALTER TABLE
                    $table
                ADD COLUMN
                    $field $description;"
            );
        }
 
        public static function alterField( $table, $oldName, $newName, $description ) {
            self::migrate(
                "ALTER TABLE
                    $table
                CHANGE
                    $oldName $newName $description;"
            );
        }
    
        public static function dropField( $table, $field ) {
            self::migrate(
                "ALTER TABLE
                    $table
                DROP COLUMN
                    $field;"
            );
        }

        public static function dropTable( $table ) {
            self::migrate(
                "DROP TABLE 
                    $table;"
            ); 
        }

        public static function dropPrimaryKey( $table ) {
            self::migrate(
                "ALTER TABLE
                    $table
                DROP PRIMARY KEY;"
            );
        } 

        public static function addPrimaryKey( $table, $name, $columns = [] ) {
            $columns = implode( ',', $columns );
            self::migrate(
                "ALTER TABLE
                    $table
                ADD CONSTRAINT $name PRIMARY KEY ( $columns );"
            );
        }

        public static function dropIndex( $table, $name ) {
            self::migrate(
                "ALTER TABLE
                    $table
                DROP INDEX
                    $name;"
            );
        }

        public static function createTable( $tableName, $fields = [], $keys = [] ) {
            $attributes = [];
            foreach ( $fields as $field => $description ) {
                if ( !empty( $field ) || !empty( $description ) ) {
                    $attributes[] = "$field $description";
                }
            }
            if ( !empty( $keys ) ) {
                $args = [];
                foreach ( $keys as $key ) {
                    if ( $key[ 'type' ] == 'unique' || $key[ 'type' ] == 'primary' ) {
                        $type = strtoupper( $key[ 'type' ] );
                        if ( is_array( $key[ 'field' ] ) ) {
                            $fields = implode( ',', $key[ 'field' ] );
                            if ( isset( $key[ 'name' ] ) ) {
                                $name = $key[ 'name' ];
                                $args[] = "CONSTRAINT $name $type KEY ( $fields )"; 
                            }
                            else {
                                $args[] = "$type KEY ( $fields )";
                            }
                        }
                        else {
                            $field = $key[ 'field' ];
                            $args[] = "$type KEY ( $field )";
                        }
                    }
                    if ( $key[ 'type' ] == 'index' ) {
                        if ( is_array( $key[ 'field' ] ) ) {
                            $fields = implode( ',', $key[ 'field' ] );
                            if ( isset( $key[ 'name' ] ) ) {
                                $name = $key[ 'name' ];
                            }
                            else {
                                $name = '';
                            }
                            $args[] = "INDEX $name ( $fields )"; 
                        }
                        else {
                            $args[] = "INDEX ( $field )";
                        }
                    }
                }
                $attributes = array_merge( $attributes, $args );
            } 
            $attributes = implode( ',', $attributes );
            self::migrate(
                "CREATE TABLE IF NOT EXISTS
                    $tableName (
                        $attributes
                    )
                ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
            );
        }
    }
?>
