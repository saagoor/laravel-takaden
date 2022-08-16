@props(['order'])
<div x-data="{ content: @js($order) }">
    <pre
        class="text-sm"
        x-text="JSON.stringify(content, null, 4)"
    >{{ $order }}</pre>
</div>
