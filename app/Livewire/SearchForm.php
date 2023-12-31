<?php

namespace App\Livewire;

use App\Jobs\ProcessPlugins;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\SimpleCache\InvalidArgumentException;

class SearchForm extends Component {
	public string $searchType = 'search';
	public string $searchTerm = 'enable-cors';

	public int $currentPage = 1;

	public int $totalPages = 1;

	public array $plugins = [];

	protected array $rules = [ 
		'searchTerm' => 'required|string|min:3',
	];

	public function resetSearch(): void {
		$this->searchTerm = '';
	}

	public function render(): \Illuminate\Contracts\Foundation\Application|Factory|View|Application {
		$this->plugins = cache()->get( $this->getCachedPluginsKey() ) ?? [];
		return view( 'livewire.search-form' );
	}

	public function search(): void {
		$this->validate();
		$this->currentPage = 1;
		$this->totalPages = 1;
		$this->plugins = [];
		try {
			$lastTime = cache()->get( $this->getLastCacheTimeKey() );
			if ( $lastTime && $lastTime + 86400 > time() ) {
				$plugins = cache()->get( $this->getCachedPluginsKey() );
				$info = cache()->get( $this->getCachedPluginsInfo() );

			} else {
				cache()->forget( $this->getCachedPluginsKey() );
				cache()->forget( $this->getCachedPluginsInfo() );
				cache()->forget( $this->getLastCacheTimeKey() );
				[ $plugins, $info ] = $this->getJson();
			}
			$this->plugins = $plugins;
			$this->currentPage = $info['page'];
			$this->totalPages = $info['pages'];
		} catch (Exception $exception) {
			Log::error( $exception->getMessage() );
		} catch (InvalidArgumentException | ContainerExceptionInterface $exception) {
			Log::error( $exception->getMessage() );
		}

	}

	private function getLastCacheTimeKey(): string {

		return "{$this->searchType}-{$this->searchTerm}-{$this->currentPage}-time";
	}

	private function getCachedPluginsKey(): string {
		return "{$this->searchType}-{$this->searchTerm}-{$this->currentPage}-results";
	}

	private function getCachedPluginsInfo(): string {
		return "{$this->searchType}-{$this->searchTerm}-{$this->currentPage}-info";
	}

	/**
	 * @throws InvalidArgumentException
	 */
	private function getJson(): array {
		try {
			$result = Http::get( "https://api.wordpress.org/plugins/info/1.2/?action=query_plugins&request[{$this->searchType}]={$this->searchTerm}&request[per_page]=100&request[page]={$this->currentPage}" );
			$plugins = $result->json( 'plugins' );
			$info = $result->json( 'info' );
			cache()->set( $this->getCachedPluginsKey(), $plugins );
			cache()->set( $this->getCachedPluginsInfo(), $info );
			cache()->set( $this->getLastCacheTimeKey(), time() );
			ProcessPlugins::dispatch( $plugins );
		} catch (Exception $exception) {
			Log::error( $exception->getMessage() );

			return [
				[], [ 
					'pages' => 1,
				],
			];
		}

		return [ $plugins, $info ];
	}

	public function tagSearch( string $tag ) {

		$this->searchType = 'tag';
		$this->searchTerm = $tag;

		$this->search();
	}

	public function nextPage(): void {
		if ( $this->currentPage < $this->totalPages ) {
			$this->currentPage++;
			try {
				$lastTime = cache()->get( $this->getLastCacheTimeKey() );
				if ( $lastTime && $lastTime + 86400 > time() ) {
					$plugins = cache()->get( $this->getCachedPluginsKey() ) ?? [];
					$info = cache()->get( $this->getCachedPluginsInfo() ) ?? [ 
						'pages' => 1,
					];

				} else {
					cache()->forget( $this->getCachedPluginsKey() );
					cache()->forget( $this->getCachedPluginsInfo() );
					cache()->forget( $this->getLastCacheTimeKey() );
					[ $plugins, $info ] = $this->getJson();
				}
				if ( count( $plugins ) > 0 ) {
					$this->plugins = array_combine( $this->plugins, $plugins );
				} else {
					$this->plugins = $plugins;
				}
				$this->totalPages = $info['pages'];
			} catch (Exception $exception) {
				Log::error( $exception->getMessage() );
			} catch (NotFoundExceptionInterface | ContainerExceptionInterface | InvalidArgumentException $exception) {
				Log::error( $exception->getMessage() );
			}
		}
	}
}
