@if ($server)
    <div class="vetra-server-context">
        <strong>{{ $server->name }}</strong>
        @if ($server->allocation)
            <span>{{ $server->allocation->display_address }}</span>
        @endif
        @if ($server->description)
            <p>{{ $server->description }}</p>
        @endif
    </div>
@endif
