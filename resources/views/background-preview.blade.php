@if ($background !== '')
    <figure>
        <figcaption>{{ trans('vetra-theme::ui.current_background') }}</figcaption>
        <img src="{{ $background }}" alt="{{ trans('vetra-theme::ui.current_background') }}"
             style="display:block;width:100%;max-height:160px;object-fit:cover;border-radius:12px;margin-top:8px" loading="lazy">
    </figure>
@endif
