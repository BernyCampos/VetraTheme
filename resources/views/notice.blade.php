@if (auth()->check() && $notice !== '')
    <aside class="vetra-notice" aria-label="{{ trans('vetra-theme::ui.notice') }}">
        <x-filament::icon icon="tabler-info-circle" class="vetra-notice-icon" />
        <div class="vetra-notice-content">{{ \Vetra\Theme\Notice::render($notice) }}</div>
    </aside>
@endif
