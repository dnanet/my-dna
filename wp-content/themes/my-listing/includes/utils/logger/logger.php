<?php

namespace MyListing\Utils\Logger;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Logger {
	use \MyListing\Src\Traits\Instantiatable;

	public $logs = [];

	public function __construct() {
		if ( CASE27_ENV !== 'dev' || ! defined( 'MYLISTING_LOG_OUTPUT' ) || MYLISTING_LOG_OUTPUT != true ) {
			return;
		}

		add_action( 'mylisting/get-footer', [ $this, 'output' ], 10e3 );
		add_action( 'admin_footer', [ $this, 'output' ], 10e3 );

		if ( ! session_id() ) {
			session_start();
		}

		if ( ! isset( $_SESSION['mlogs'] ) ) {
			$_SESSION['mlogs'] = [];
		}

		$this->logs = &$_SESSION['mlogs'];
	}

	public function log( $content, $type ) {
		$this->logs[] = [
			'content' => $content,
			'type' => $type,
			'seen' => false,
			'trace' => $this->format_backtrace( debug_backtrace() ),
		];
	}

	public function info( $content ) {
		$this->log( $content, 'info' );
	}

	public function warn( $content ) {
		$this->log( $content, 'warning' );
	}

	public function output() {
		$categories = [];
		foreach ( $this->logs as $key => $log ) {
			if ( $log['seen'] ) {
				unset( $this->logs[ $key ] );
				continue;
			}

			$this->logs[ $key ]['seen'] = true;
			if ( ! isset( $categories[ $log['type'] ] ) ) {
				$categories[ $log['type'] ] = 0;
			}
			$categories[ $log['type'] ]++;
		}

		if ( empty( $this->logs ) ) {
			return;
		}

		require locate_template( 'includes/utils/logger/templates/output.php' );
	}

	public function dump( $expression ) {
		echo '<pre>';
			foreach ( func_get_args() as $expression ) {
				var_dump( $expression );
				echo '<hr>';
			}
		echo '</pre>';
	}

	public function dd() {
		foreach ( func_get_args() as $expression ) {
			$this->dump( $expression );
		}

		$this->output();
		die;
	}

	/**
	 * Print out a stack trace from entry point to wherever this function was called.
	 * @param boolean $show_args Show arguments passed to functions? Default False.
	 * @param boolean $for_web Format text for web? Default True.
	 * @param boolean $return Return result instead of printing it? Default False.
	 * @link https://gist.github.com/JaggedJax/3837352
	 */
	public function format_backtrace( $backtrace, $show_args = false ){
		$before = '<span>';
		$after = '</span>';
		$tab = '&nbsp;&nbsp;&nbsp;&nbsp;';
		$newline = '<br>';
		$output = '';
		$ignore_functions = array( 'include', 'include_once', 'require', 'require_once' );
		$length = count( $backtrace );

		// Start from index 1 to hide redundant line(s).
		for ( $i=1; $i<$length; $i++ ) {
			$function = '';
			$line = '<div class="cts-backtrace-log"><span class="cts-log-index">' . ($i) . '. </span>';
			$skip_args = false;
			$caller = @$backtrace[$i+1]['function'];
			// Display caller function (if not a require or include)
			if ( isset( $caller ) && ! in_array( $caller, $ignore_functions ) ) {
				$function = ' [fn:'.$caller.'()]';
			} else {
				$skip_args = true;
			}

			$line_nr = ! empty( $backtrace[$i]['line'] ) ? $backtrace[$i]['line'] : '(line:n/a)';
			$dir = ! empty( $backtrace[$i]['file'] ) ? dirname( $backtrace[$i]['file'] ) : '(dir:n/a)';
			$file = ! empty( $backtrace[$i]['file'] ) ? basename( $backtrace[$i]['file'] ) : '(file:n/a)';

			$line .= sprintf( '<em>%s<b>/%s:%s</b></em>', $dir, $file, $line_nr );

			$line .= $function.$newline;
			if ($i < $length-1){
				if ($show_args && $backtrace[($i+1)]['args'] && !$skip_args){
					$params = htmlentities(print_r($backtrace[($i+1)]['args'], true));
					$line .= $tab.'Called with params: '.preg_replace('/(\n)/',$newline.$tab,trim($params)).$newline.$tab.'By:'.$newline;
					unset($params);
				}
			}

			$line .= '</div>';
			$output .= $line;
		}

		return $output;
	}
}