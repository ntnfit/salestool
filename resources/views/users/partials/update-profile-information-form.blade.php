<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-adminlte-input label="Full name" label-class="text-lightblue" type="text" name="name" id="" placeholder="Nguyen van A"  fgroup-class="col-md-6" enable-old-support value="{{$user->name}}">
                <x-slot name="prependSlot">
                    <div class="input-group-text">
                        <i class="fas fa-id-card text-lightblue"></i>
                    </div>
                </x-slot>
                </x-adminlte-input>
        </div>

        <div>
            <x-adminlte-input label="Email"  label-class="text-lightblue" name="email" type="email" placeholder="mail@example.com"  fgroup-class="col-md-6" enable-old-support value="{{$user->email}}" readonly="true">
                <x-slot name="prependSlot">
                    <div class="input-group-text">
                        <i class="fas fa-envelope text-lightblue"></i>
                    </div>
                </x-slot>
                </x-adminlte-input>
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </form>
</section>
