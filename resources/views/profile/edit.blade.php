<h1>Edit Profile</h1>

@if (session('status') === 'profile-updated')
    <p style="color: green;">Profile has been updated.</p>
@endif
@if (session('status') === 'password-updated')
    <p style="color: green;">Password has been updated.</p>
@endif

<h2>Update Profile Information</h2>
<form method="POST" action="{{ route('profile.update') }}">
    @csrf
    @method('PATCH')

    <div>
        <label for="name">Name</label>
        <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
        @error('name', 'updateProfileInformation')
            <span>{{ $message }}</span>
        @enderror
    </div>

    <div>
        <label for="email">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="username">
        @error('email', 'updateProfileInformation')
            <span>{{ $message }}</span>
        @enderror
    </div>

    <div>
        <button type="submit">Update Profile</button>
    </div>
</form>

<hr>

<h2>Update Password</h2>
<form method="POST" action="{{ route('profile.password.update') }}">
    @csrf
    @method('PUT')

    <div>
        <label for="current_password">Current Password</label>
        <input id="current_password" type="password" name="current_password" required autocomplete="current-password">
        @error('current_password', 'updatePassword')
            <span>{{ $message }}</span>
        @enderror
    </div>

    <div>
        <label for="password">New Password</label>
        <input id="password" type="password" name="password" required autocomplete="new-password">
        @error('password', 'updatePassword')
            <span>{{ $message }}</span>
        @enderror
    </div>

    <div>
        <label for="password_confirmation">Confirm New Password</label>
        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
        @error('password_confirmation', 'updatePassword')
            <span>{{ $message }}</span>
        @enderror
    </div>

    <div>
        <button type="submit">Update Password</button>
    </div>
</form>

<hr>

<h2>Delete Account</h2>
<form method="POST" action="{{ route('profile.destroy') }}">
    @csrf
    @method('DELETE')

    <p>Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.</p>

    <div>
        <label for="delete_password">Password</label>
        <input id="delete_password" type="password" name="password" required autocomplete="current-password">
        @error('password', 'deleteUser') <!-- Assuming error bag name from Breeze -->
            <span>{{ $message }}</span>
        @enderror
    </div>

    <div>
        <button type="submit" onclick="return confirm('Are you sure you want to delete your account?');">Delete Account</button>
    </div>
</form>
