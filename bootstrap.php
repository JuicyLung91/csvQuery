<?php function autoload( $class ) {
  preg_match('/^(.+)?([^\\\\]+)$/U', ltrim( $class, '\\' ), $match );
  require str_replace( '\\', '/', $match[ 1 ] )
      . str_replace( [ '\\', '_' ], '/', $match[ 2 ] )
      . '.php';
}

spl_autoload_register('autoload');


require ('Example.php');