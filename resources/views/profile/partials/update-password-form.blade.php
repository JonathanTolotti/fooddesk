<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Atualização de senha') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400 mt-0.5">
            {{ __('Garanta que a senha escolhida seja segura o suficiente.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5" for="update_password_current_password" :value="__('Senha atual')" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5" for="update_password_password" :value="__('Noa senha')" />
            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5" for="update_password_password_confirmation" :value="__('Confirme a nova senha')" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="px-5 py-2 text-sm font-semibold rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition-colors">{{ __('Salvar') }}</x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
