<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessPlugins implements ShouldQueue {
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	public array $plugins;

	/**
	 * Create a new job instance.
	 */
	public function __construct( array $plugins ) {
		$this->plugins = $plugins;
	}

	/**
	 * Execute the job.
	 */
	public function handle(): void {
		$plugins = collect( $this->plugins );
		$plugins->each( function ($plugin) {
			$this->add( $plugin );
		} );
	}

	private function add( $plugin ) {
		\Log::info( $plugin['slug'] );
	}

}
