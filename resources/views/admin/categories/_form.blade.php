<div>
    <x-form.label for="name" required>Nombre</x-form.label>
    <x-form.input id="name" name="name" :value="old('name', optional($category ?? null)->name)" required maxlength="80" autofocus />
    <p class="mt-1.5 text-xs text-secondary-light">El identificador se genera a partir del nombre y permanece estable después de crearla.</p>
</div>
