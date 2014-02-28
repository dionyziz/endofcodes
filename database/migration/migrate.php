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

            require_once $pref . 'config/config-local.php';
            require_once $pref . 'models/database.php';
            require_once $pref . 'models/db.php';


            $config = getConfig()[ $env ]; 
            dbInit();

            try {
                $res = db( $sql );
            }
            catch ( DBException $e ) {
                throw new MigrationException( $e );
            }
        } 
        
        public static function run( $sql ) {
            try {
                self::migrate( $sql );
            }
            catch ( MigrationException $e ) {
                throw $e;
            }
            echo "Migration successful.\n"; 
        }

        public static function addField( $table, $field, $description ) {
            self::run( 
                "ALTER TABLE
                    $table
                ADD COLUMN
                    $field $description;"
            );
        }
 
        public static function alterField( $table, $oldName, $newName, $description ) {
            self::run(
                "ALTER TABLE
                    $table
                CHANGE
                    $oldName $newName $description;"
            );
        }
    
        public static function dropField( $table, $field ) {
            self::run(
                "ALTER TABLE
                    $table
                DROP COLUMN
                    $field;"
            );
        }

        public static function dropTable( $table ) {
            self::run(
                "DROP TABLE 
                    $table;"
            ); 
        }

        public static function dropPrimaryKey( $table ) {
            self::run(
                "ALTER TABLE
                    $table
                DROP PRIMARY KEY;"
            );
        } 

        public static function addPrimaryKey( $table, $name, $columns = [] ) {
            $columns = implode( ',', $columns );
            self::run(
                "ALTER TABLE
                    $table
                ADD CONSTRAINT $name PRIMARY KEY ( $columns );"
            );
        }

        public static function dropIndex( $table, $name ) {
            self::run(
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
                    if ( $key[ 'type' ] == 'unique' || $key[ 'type' ] == 'primary' || $key[ 'type' ] == 'foreign' ) {
                        $type = strtoupper( $key[ 'type' ] );
                        if ( isset( $key[ 'name' ] ) ) {
                            $fields = implode( ',', $key[ 'field' ] );
                            $name = $key[ 'name' ];
                            $args[] = "CONSTRAINT $name $type KEY ( $fields )"; 
                        }
                        else {
                            foreach ( $key[ 'field' ] as $field ) {
                                $args[] = "$type KEY( $field )";
                            }
                        }
                    }
                }
                if ( $key[ 'type' ] == 'index' ) {
                    if ( isset( $key[ 'name' ] ) ) {
                        $fields = implode( ',', $key[ 'field' ] );
                        $name = $key[ 'name' ];
                        $args[] = "INDEX $name ( $fields )"; 
                    }
                    else {
                        foreach ( $key[ 'field' ] as $field ) {
                            $args[] = "INDEX( $field )";
                        }
                    }
                }
                $attributes = array_merge( $attributes, $args );
            } 
            $attributes = implode( ',', $attributes );
            self::run(
                "CREATE TABLE IF NOT EXISTS
                    $tableName (
                        $attributes
                    )
                ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
            );
        }
    }
?>
