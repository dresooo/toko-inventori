<!-- resources/views/components/signup-modal.blade.php -->

<dialog id="signup_modal" class="modal">
    <div class="modal-box p-10 max-w-md">
        <h3 class="font-bold text-lg text-center mb-5">Sign Up</h3>
        <form id="signupForm" class="space-y-4 mt-4">
            <h2>Nama Lengkap</h2>
            <input type="text" id="signupName" placeholder="Nama Lengkap" class="input input-bordered w-full"
                required />

            <h2>Nomor Telepon</h2>
            <input type="tel" id="signupPhone" placeholder="Nomor Telepon" class="input input-bordered w-full"
                required />

            <h2>Email</h2>
            <input type="email" id="signupEmail" placeholder="Email" class="input input-bordered w-full" required />

            <h2>Password</h2>
            <input type="password" id="signupPassword" placeholder="Password" class="input input-bordered w-full"
                required />

            <h2>Alamat Lengkap</h2>
            <textarea id="signupAddress" placeholder="Alamat Lengkap" class="textarea textarea-bordered w-full" rows="3"
                required></textarea>

            <button type="submit" class="btn btn-primary w-full">Sign Up</button>
        </form>

        <h2 class="mt-4 text-center">
            Already have an account?
            <span class="font-bold cursor-pointer hover:underline"
                onclick="document.getElementById('signup_modal').close(); document.getElementById('login_modal').showModal();">
                Login
            </span>
        </h2>

        <!-- Tombol close -->
        <div class="modal-action flex justify-between items-center">
            <button class="btn" onclick="document.getElementById('signup_modal').close()">Close</button>
        </div>
    </div>

    <!-- Backdrop -->
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>