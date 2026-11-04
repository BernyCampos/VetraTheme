@if ($stylesheet !== '')
    <style id="vetra-theme">{!! $stylesheet !!}</style>
@endif
<style id="vetra-variables">body:has(#vetra-ambient) { {!! $css !!} }</style>
