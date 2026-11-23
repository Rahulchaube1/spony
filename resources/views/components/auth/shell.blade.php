@props([
    'title',
    'description' => null,
])

<section class="auth-shell application-settings-form">
    <div class="auth-shell-content">
        <div class="auth-card">
            <div class="auth-card-heading">
                <div class="flex items-center justify-center mb-4">
                    <img src="/vyntra-logo.png" alt="Vyntra" class="size-14 object-contain rounded-xl shadow-md" />
                </div>
                <h1>{{ $title }}</h1>
                @if ($description)
                    <p>{{ $description }}</p>
                @endif
            </div>

            <div class="auth-card-body">
                {{ $slot }}
            </div>

            <p class="flex justify-center py-3"><a class="eq-family-link" href="https://www.everestqai.com/products">All EverestQ products →</a></p>
            @isset($footer)
                <footer class="auth-card-footer">
                    {{ $footer }}
                </footer>
            @endisset
        </div>
    </div>
</section>
