<?php
    abstract class Migration {
        public static $log = 'database/migration/.history';
        public static $path = 'database/migration/';
        public static $environments = [ 'development', 'test' ];

        public static function loadLog() {
            try {
                $json = safeRead( static::$log );
                $array = json_decode( $json, true );
            }
            catch ( FileNotFoundException $e ) {
                $array = [];
            }
            return $array;
        }

        public static function writeLog() {
            $json = json_encode( $log );
            safeWrite( static::$log, $json );
        }

        public static function updateLog( $name, $environment ) {
            $log = self::loadLog();
            $log[ $environment ] = $name;
            self::writeLog();
        }

        public static function findAll() {
            $array = [];
            foreach ( glob( static::$path . '*.php' ) as $filename ) {
                $filename = str_replace( static::$path, '', $filename );
                $array[] = $filename;
            }
            sort( $array );
            return $array;
        }

        public static function findUnexecuted( $environment ) {
            $allMigrations = self::findAll();
            $log = self::loadLog();
            if ( !isset( $log[ $environment ] ) ) {
                return $allMigrations;
            }
            $lastMigrationRun = $log[ $environment ];
            $position = array_search( $lastMigrationRun, $allMigrations );
            $unexecuted = array_slice( $allMigrations, $position + 1 );
            return $unexecuted;
        }

        public static function migrate( $sql ) {
            try {
                db( $sql );
            }
            catch ( DBException $e ) {
                throw new MigrationException( $e );
            }
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
