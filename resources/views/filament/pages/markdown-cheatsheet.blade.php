<div class="grid gap-6 text-sm sm:grid-cols-2">
    <div>
        <h4 class="mb-3 font-medium text-gray-950 dark:text-white">
            {{ __('admin.pages.cheatsheet.syntax.heading') }}
        </h4>

        <dl class="space-y-2">
            @foreach (__('admin.pages.cheatsheet.syntax.rows') as $row)
                <div class="flex items-baseline justify-between gap-4">
                    <dt class="text-gray-500 dark:text-gray-400">{{ $row['label'] }}</dt>
                    <dd>
                        <code class="rounded bg-gray-100 px-1.5 py-0.5 text-xs dark:bg-white/5">{{ $row['example'] }}</code>
                    </dd>
                </div>
            @endforeach
        </dl>
    </div>

    <div>
        <h4 class="mb-3 font-medium text-gray-950 dark:text-white">
            {{ __('admin.pages.cheatsheet.front_matter.heading') }}
        </h4>

        <p class="text-gray-500 dark:text-gray-400">{{ __('admin.pages.cheatsheet.front_matter.description') }}</p>

        <pre class="mt-3 overflow-x-auto rounded-lg bg-gray-100 p-3 text-xs dark:bg-white/5"><code>{{ __('admin.pages.cheatsheet.front_matter.example') }}</code></pre>

        <p class="mt-3 text-gray-500 dark:text-gray-400">{{ __('admin.pages.cheatsheet.attachments') }}</p>
    </div>
</div>
