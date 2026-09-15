<div class="auth-card glass-panel">
    <div class="auth-card__header">
        <h1 class="auth-card__title">Iniciar sesión</h1>
        <p class="auth-card__subtitle">Ingresa con tu cuenta de administrador FESC.</p>
    </div>

    <form
        method="POST"
        action="{{ route('login') }}"
        class="auth-card__form"
        novalidate
        x-data="{ submitting: false }"
        @submit="submitting = true"
    >
        @csrf

        <div>
            <x-form.label for="email" required class="!font-semibold !text-secondary">Correo electrónico</x-form.label>
            <x-form.input
                id="email"
                name="email"
                type="email"
                :value="old('email')"
                required
                autofocus
                autocomplete="username"
                placeholder="correo@fesc.edu.co"
                inputmode="email"
                class="auth-card__input"
            />
        </div>

        <div>
            <x-form.label for="password" required class="!font-semibold !text-secondary">Contraseña</x-form.label>
            <x-form.password
                id="password"
                name="password"
                required
                autocomplete="current-password"
                class="auth-card__input"
            />
        </div>

        <div class="auth-card__row">
            <x-form.checkbox name="remember" id="remember">Recordarme</x-form.checkbox>
        </div>

        <x-ui.button
            type="submit"
            size="lg"
            class="auth-card__submit w-full !rounded-2xl !px-6 !shadow-[0_12px_28px_rgb(200_16_46_/_0.22)]"
            x-bind:disabled="submitting"
            x-bind:aria-busy="submitting"
        >
            <span x-show="!submitting">Iniciar sesión</span>
            <span x-cloak x-show="submitting">Iniciando sesión...</span>
        </x-ui.button>
    </form>
</div>
