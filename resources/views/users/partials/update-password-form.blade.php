<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.updatepass') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div class="col-md-6">
            <x-adminlte-input label="password"  label-class="text-lightblue" name="password" type="password"   fgroup-class="col-md-6" >
                <x-slot name="prependSlot">
                    <div class="input-group-text">
                        <i class="fas fa-lock text-lightblue"></i>
                    </div>
                </x-slot>
            </x-adminlte-input>
        </div>
        <div class="col-md-6">
            <x-adminlte-input label="confirm password"  label-class="text-lightblue" name="confirm-password" type="password"   fgroup-class="col-md-6" >
                <x-slot name="prependSlot">
                    <div class="input-group-text">
                        <i class="fas fa-lock text-lightblue"></i>
                    </div>
                </x-slot>
            </x-adminlte-input>

        <div class="flex items-center gap-4">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </form>
</section>
