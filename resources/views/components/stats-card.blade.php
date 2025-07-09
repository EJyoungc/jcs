@props(['title', 'count'])


<div class="card">
    <div class="card-body text-center">
        <h5 class="card-title">{{ $title }}</h5>
        <p class="card-text display-4">{{ $count }}</p>
    </div>
</div>