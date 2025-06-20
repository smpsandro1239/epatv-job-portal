<form method="POST" action="{{ route('password.confirm') }}">
    @csrf
    <div>
        <label for="password">Password</label>
        <input id="password" type="password" name="password" required autocomplete="current-password">
        @error('password')
            <span>{{ $message }}</span>
        @enderror
    </div>
    <div>
        <button type="submit">
            Confirm Password
        </button>
    </div>
</form>
