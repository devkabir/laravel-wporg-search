<div>
    @if (session()->has('success'))
        <div class="alert alert-success" role="alert">
            {{ session()->get('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger" role="alert">
            {{ session()->get('error') }}
        </div>
    @endif
    <h1 class="mb-4 text-2xl font-bold">Search</h1>

    <div>
        <div class="sm:flex">
            <select wire:model="searchType"
                class="relative block w-full px-3 py-2 -mt-px text-sm border-gray-200 shadow-sm pe-9 sm:w-auto -ms-px first:rounded-t-lg last:rounded-b-lg sm:first:rounded-s-lg sm:mt-0 sm:first:ms-0 sm:first:rounded-se-none sm:last:rounded-es-none sm:last:rounded-e-lg focus:z-10 focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-gray-400 dark:focus:ring-gray-600">
                <option value="search" {{ $searchType == 'search' ? 'selected' : '' }}>Search</option>
                <option value="tag" {{ $searchType == 'tag' ? 'selected' : '' }}>Tag</option>
                <option value="author" {{ $searchType == 'author' ? 'selected' : '' }}>Author</option>
            </select>
            <input id="af-account-phone" type="text" wire:model="searchTerm"
                class="relative block w-full px-3 py-2 -mt-px text-sm border-gray-200 shadow-sm pe-11 -ms-px first:rounded-t-lg last:rounded-b-lg sm:first:rounded-s-lg sm:mt-0 sm:first:ms-0 sm:first:rounded-se-none sm:last:rounded-es-none sm:last:rounded-e-lg focus:z-10 focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-gray-400 dark:focus:ring-gray-600"
                placeholder="enable-cors">
        </div>
        <div class="flex mt-2 space-x-2">
            <button class="px-4 py-2 text-white bg-indigo-500 rounded" type="button" wire:click.prevent="search">Search
            </button>
            <button class="px-4 py-2 text-white rounded bg-rose-500" type="button"
                wire:click.prevent="resetSearch">Reset
            </button>
        </div>

    </div>

    @if ($currentPage !== $totalPages)
        <h1 class="mb-4 text-2xl font-bold">Showing Results of {{ $currentPage }} Page outof {{ $totalPages }}
            Pages
        </h1>
        <div class="flex justify-center">
            <button class="px-4 py-2 text-white bg-indigo-500 rounded" type="button" wire:click.prevent="nextPage">Next
            </button>
        </div>
    @endif



    <div wire:loading>
        Searching ...
    </div>

    <table wire:loading.remove
        class="max-w-2xl min-w-full mt-6 bg-white divide-y divide-gray-200 rounded shadow dark:divide-gray-700"
        id="searchTable">
        <thead class="bg-gray-50 dark:bg-slate-800">
            <tr>
                <th scope="col" class="px-6 py-3 text-start">
                    <div class="flex items-center gap-x-2">
                        <span class="text-xs font-semibold tracking-wide text-gray-800 uppercase dark:text-gray-200">
                            Name
                        </span>
                    </div>
                </th>

                <th scope="col" class="px-6 py-3 text-start">
                    <div class="flex items-center gap-x-2">
                        <span class="text-xs font-semibold tracking-wide text-gray-800 uppercase dark:text-gray-200">
                            Dates
                        </span>
                    </div>
                </th>

                <th scope="col" class="px-6 py-3 text-start">
                    <div class="flex items-center gap-x-2">
                        <span class="text-xs font-semibold tracking-wide text-gray-800 uppercase dark:text-gray-200">
                            Status
                        </span>
                    </div>
                </th>

                <th scope="col" class="px-6 py-3 text-start">
                    <div class="flex items-center gap-x-2">
                        <span class="text-xs font-semibold tracking-wide text-gray-800 uppercase dark:text-gray-200">
                            Threads
                        </span>
                    </div>
                </th>

                <th scope="col" class="px-6 py-3 text-start">
                    <div class="flex items-center gap-x-2">
                        <span class="text-xs font-semibold tracking-wide text-gray-800 uppercase dark:text-gray-200">
                            Tags
                        </span>
                    </div>
                </th>

            </tr>
        </thead>

        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @foreach ($plugins as $plugin)
                @php
                    $date = DateTime::createFromFormat('Y-m-d h:ia T', $plugin['last_updated']);
                    $currentYear = (int) date('Y');

                    $active = $date->format('Y') == $currentYear;
                @endphp
                <tr>

                    <td class="w-px h-px ">
                        <div class="px-6 py-3">
                            <div class="flex items-center gap-x-3">
                                <img class="inline-block h-[2.375rem] w-[2.375rem]"
                                    src="{{ $plugin['icons']['1x'] ?? $plugin['icons']['default'] }}"
                                    alt="Image Description">
                                <div>
                                    <a class="grow" href="https://wordpress.org/plugins/{{ $plugin['slug'] }}"
                                        target="_blank">
                                        <span
                                            class="block text-sm font-semibold text-indigo-800 dark:text-gray-200">{!! $plugin['name'] !!}</span>
                                    </a>
                                    {!! $plugin['author'] !!}
                                </div>

                            </div>
                        </div>
                    </td>
                    <td class="w-px h-px ">
                        <div class="px-6 py-3">
                            <span
                                class="block text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $plugin['added'] }}</span>
                            <span class="block text-sm text-gray-500">{{ $plugin['last_updated'] }}</span>
                        </div>
                    </td>
                    <td class="w-px h-px ">
                        <div class="px-6 py-3">
                            <span
                                class="py-1 px-1.5 inline-flex items-center gap-x-1 text-xs font-medium rounded-full {{ $active ? 'bg-teal-100 text-teal-800  dark:bg-teal-500/10 dark:text-teal-500' : 'bg-rose-100 text-rose-800' }} ">

                                {{ $active ? 'Active' : 'Inactive' }}
                            </span>
                            {{ Number::forHumans($plugin['active_installs']) }}
                        </div>
                    </td>
                    <td class="w-px h-px ">
                        <div class="px-6 py-3">
                            <div class="items-center gap-x-3">
                                <span
                                    class="text-xs text-gray-500">{{ $plugin['support_threads_resolved'] }}/{{ $plugin['support_threads'] }}</span>
                                <div
                                    class="flex w-full h-1.5 bg-gray-200 rounded-full overflow-hidden dark:bg-gray-700">
                                    <div class="flex flex-col justify-center overflow-hidden bg-gray-800 dark:bg-gray-200"
                                        role="progressbar"
                                        style="width: {{ $plugin['support_threads'] > 0 ? ($plugin['support_threads_resolved'] / $plugin['support_threads']) * 100 : 100 }}%"
                                        aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="w-px h-px ">
                        <div class="px-6 py-3">
                            @foreach ($plugin['tags'] as $tag)
                                <button wire:click="tagSearch('{{ $tag }}')"
                                    class="py-1 px-1.5 inline-flex items-center gap-x-1 text-xs font-medium rounded-full bg-teal-100 text-teal-800 dark:bg-teal-500/10 dark:text-teal-500">
                                    {{ $tag }}
                                </button>
                            @endforeach

                        </div>
                    </td>

                </tr>
            @endforeach
        </tbody>

    </table>
