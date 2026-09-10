<div class="auth-card rounded-[1.5rem] p-6 sm:rounded-[2rem] sm:p-9">
    <div class="mb-6 sm:mb-7">
        <h1 class="font-serif text-2xl font-bold text-secondary sm:text-3xl">Iniciar sesión</h1>
        <p class="mt-1.5 text-sm text-secondary-light">Ingresa con tu cuenta de administrador.</p>
    </div>

    <form
        method="POST"
        action="{{ route('login') }}"
        class="space-y-4 sm:space-y-5"
        novalidate
        x-data="{ submitting: false }"
        @submit="submitting = true"
    >
        @csrf

        <div>
            <x-form.label for="email" required>Correo electrónico</x-form.label>
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
            />
        </div>

        <div>
            <x-form.label for="password" required>Contraseña</x-form.label>
            <x-form.password
                id="password"
                name="password"
                required
                autocomplete="current-password"
            />
        </div>

        <x-form.checkbox name="remember" id="remember">Recordarme</x-form.checkbox>

        <x-ui.button type="submit" class="w-full !font-bold" x-bind:disabled="submitting" x-bind:aria-busy="submitting">
            <span x-show="!submitting">Iniciar sesión</span>
            <span x-cloak x-show="submitting">Iniciando sesión...</span>
        </x-ui.button>
    </form>
</div>
