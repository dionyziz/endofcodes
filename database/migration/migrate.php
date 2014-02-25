<?php
    abstract class Migration {
        protected static function migrate( $sql ) {
            global $config;
            $env = getEnv( 'ENVIRONMENT' );

            if ( $env === false ) { 
                $pref = "";
                $env = 'test';
            }
            else {
                $pref = "../../";
            }

            include_once $pref . 'config/config-local.php';
            include_once $pref . 'models/database.php';
            include_once $pref . 'models/db.php';


            $config = getConfig()[ $env ]; 
            dbInit();

            try {
                $res = db( $sql );
            }
            catch ( DBException $e ) {
                throw new MigrationException( $e );
            }
            echo "Migration successful.\n";
        } 
    
        public static function addField( $table, $field, $description ) {
            self::migrate( "ALTER TABLE
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
		    foreach ( $fields as $field => $description ) {
                if ( !empty( $field ) || !empty( $description ) ) {
                    $attributes[] = "$field $description";
                }
		    }
            if ( !empty( $keys ) ) {
                foreach ( $keys as $key ) {
                    if ( $key[ 'type' ] == 'unique' || $key[ 'type' ] == 'primary' || $key[ 'type' ] == 'foreign' ) {
                        if ( isset( $key[ 'name' ] ) ) {
                            $fields = implode( ',', $key[ 'field' ] );
                            $name = $key[ 'name' ];
                            $args[] = "CONSTRAINT $name PRIMARY KEY ( $fields )"; 
                        }
                        else {
                            $type = strtoupper( $key[ 'type' ] );
                            foreach ( $key[ 'field' ] as $field ) {
                                $args[] = "$type KEY ( $field )";
                            }
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
