<?php
    abstract class Migration {
        protected static function migrate( $sql ) {
            try {
                $res = db( $sql );
            }
            catch ( DBException $e ) {
                throw new MigrationException( $e );
            }
        } 

        public static function createLog( $name, $env ) {
            $path = 'database/migration/.history';
            if ( !$fh = fopen( $path, 'w' ) ) {
                throw new ModelNotFoundException();
            }
            fwrite( $fh, "$env: $name\n" );
            fclose( $fh );
        }

        public static function findUnexecuted( $env = '' ) {
            if ( empty( $env ) ) {
                $environments = [ 'development', 'test' ];
                $list = [];
                foreach ( $environments as $env ) {
                    $list[ $env ]  = self::getUnexecuted( $env );
                }
                return $list;
            } 
            return self::getUnexecuted( $env );
        }

        protected static function getUnexecuted( $env ) {
            try {
                $last = self::findLast( $env );
            }
            catch ( ModelNotFoundException $e ) {
                return self::findAll();
            }
            $migrations = self::findAll();
            $position = array_search( trim( $last ), $migrations );
            $ran = array_slice( $migrations, 0, $position + 1 );
            return array_diff( $migrations, $ran );
        }

        public static function findLast( $env = '' ) {
            if ( !$logs = file_get_contents( 'database/migration/.history' ) ) {
                throw new ModelNotFoundException();
            }
            if ( empty( $env ) ) {
                $environments = [ 'development', 'test' ];
                $last = [];
                foreach ( $environments as $env ) {
                    $last[ $env ] = self::getLast( $env, $logs );
                }
                return $last;
            }
            return self::getLast( $env, $logs );
        }

        protected static function getLast( $env, $logs ) {
            $result = explode( "$env:", $logs );
            if ( count( $result ) <= 1 ){
                return;
            }
            $result_split = explode( ' ', $result[ 1 ] );
            return $result_split[ 1 ];
        }
            
        public static function findAll() {
            $array = [];
            $dir = 'database/migration/';
            foreach ( glob( $dir . '*.php' ) as $filename) {
                $filename = str_replace( $dir, '', $filename );
                $array[] = $filename;
            }
            sort( $array );
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
