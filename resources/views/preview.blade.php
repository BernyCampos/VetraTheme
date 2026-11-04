<figure class="vetra-preview {{ $settings['mode'] === 'light' ? 'vetra-preview-light' : '' }}" style="{{ \Vetra\Theme\Appearance::css($settings) }}">
    <figcaption>{{ trans('vetra-theme::ui.preview') }}</figcaption>
    <div class="vetra-preview-surface">
        <span class="vetra-preview-swatch"></span>
        <div><strong>VetraTheme</strong><p>{{ trans('vetra-theme::ui.preview_text') }}</p></div>
    </div>
</figure>
