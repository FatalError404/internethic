<?php
	class CsvService{
		/**
		 *
		 */
		function generate( $array ){
			$now = time() ;
			$path = "./var/storage/audit.$now.csv" ;
			$separator = ';';
			$ressource = fopen( $path, 'w+' ) ;
			foreach( $array as $row ){
				fputcsv( $ressource, $row, $separator ) ;
			}
			fclose( $ressource ) ;
		}
	}